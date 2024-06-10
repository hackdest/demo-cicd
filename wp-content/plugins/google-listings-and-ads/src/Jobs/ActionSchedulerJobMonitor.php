<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\GoogleListingsAndAds\Jobs;

use Automattic\WooCommerce\GoogleListingsAndAds\ActionScheduler\ActionSchedulerInterface;
use Automattic\WooCommerce\GoogleListingsAndAds\Infrastructure\Service;
use Automattic\WooCommerce\GoogleListingsAndAds\PluginHelper;

defined( 'ABSPATH' ) || exit;

/**
 * Class ActionSchedulerJobMonitor
 *
 * @package Automattic\WooCommerce\GoogleListingsAndAds\Jobs
 */
class ActionSchedulerJobMonitor implements Service {

	use PluginHelper;

	/**
	 * @var ActionSchedulerInterface
	 */
	protected $action_scheduler;

	/**
	 * @var bool[] Array of `true` values for each job that is monitored. A hash string generated by `self::get_job_hash`
	 *      is used as keys.
	 */
	protected $monitored_hooks = [];

	/**
	 * ActionSchedulerInterface constructor.
	 *
	 * @param ActionSchedulerInterface $action_scheduler
	 */
	public function __construct( ActionSchedulerInterface $action_scheduler ) {
		$this->action_scheduler = $action_scheduler;
	}

	/**
	 * Check whether the failure rate is above the specified threshold within the timeframe.
	 *
	 * To protect against failing jobs running forever the job's failure rate is checked before creating a new batch.
	 * By default, a job is stopped if it has 5 failures in the last hour.
	 *
	 * @param ActionSchedulerJobInterface $job
	 * @param string                      $hook The job action hook.
	 * @param array|null                  $args The job arguments.
	 *
	 * @throws JobException If the job's error rate is above the threshold.
	 */
	public function validate_failure_rate( ActionSchedulerJobInterface $job, string $hook, ?array $args = null ) {
		if ( $this->is_failure_rate_above_threshold( $hook, $args ) ) {
			throw JobException::stopped_due_to_high_failure_rate( $job->get_name() );
		}
	}

	/**
	 * Reschedules the job if it has failed due to timeout.
	 *
	 * @param string     $hook The job action hook.
	 * @param array|null $args The job arguments.
	 *
	 * @since 1.7.0
	 */
	public function attach_timeout_monitor( string $hook, ?array $args = null ) {
		$this->monitored_hooks[ self::get_job_hash( $hook, $args ) ] = true;
		add_action(
			'action_scheduler_unexpected_shutdown',
			[ $this, 'reschedule_if_timeout' ],
			10,
			2
		);
	}

	/**
	 * Detaches the timeout monitor that handles rescheduling jobs on timeout.
	 *
	 * @param string     $hook The job action hook.
	 * @param array|null $args The job arguments.
	 *
	 * @since 1.7.0
	 */
	public function detach_timeout_monitor( string $hook, ?array $args = null ) {
		unset( $this->monitored_hooks[ self::get_job_hash( $hook, $args ) ] );
		remove_action( 'action_scheduler_unexpected_shutdown', [ $this, 'reschedule_if_timeout' ] );
	}

	/**
	 * Reschedules an action if it has failed due to a timeout error.
	 *
	 * The number of previous failures will be checked before rescheduling the action, and it must be below the
	 * specified threshold in `self::get_failure_rate_threshold` within the timeframe specified in
	 * `self::get_failure_timeframe` for the action to be rescheduled.
	 *
	 * @param int   $action_id
	 * @param array $error
	 *
	 * @since 1.7.0
	 */
	public function reschedule_if_timeout( $action_id, $error ) {
		if ( ! empty( $error ) && $this->is_timeout_error( $error ) ) {
			$action = $this->action_scheduler->fetch_action( $action_id );
			$hook   = $action->get_hook();
			$args   = $action->get_args();

			// Confirm that the job is initiated by GLA and monitored by this instance.
			// The `self::attach_timeout_monitor` method will register the job's hook and arguments hash into the $monitored_hooks variable.
			if ( $this->get_slug() !== $action->get_group() || ! $this->is_monitored_for_timeout( $hook, $args ) ) {
				return;
			}

			// Check if the job has not failed more than the allowed threshold.
			if ( $this->is_failure_rate_above_threshold( $hook, $args ) ) {
				do_action(
					'woocommerce_gla_debug_message',
					sprintf( 'The %s job failed too many times, not rescheduling.', $hook ),
					__METHOD__
				);

				return;
			}

			do_action(
				'woocommerce_gla_debug_message',
				sprintf( 'The %s job has failed due to a timeout error, rescheduling...', $hook ),
				__METHOD__
			);

			$this->action_scheduler->schedule_immediate( $hook, $args );
		}
	}

	/**
	 * Determines whether the given error is an execution "timeout" error.
	 *
	 * @param array $error An associative array describing the error with keys "type", "message", "file" and "line".
	 *
	 * @return bool
	 *
	 * @link https://www.php.net/manual/en/function.error-get-last.php
	 *
	 * @since 1.7.0
	 */
	protected function is_timeout_error( array $error ): bool {
		return isset( $error['type'] ) && $error['type'] === E_ERROR &&
			isset( $error['message'] ) && strpos( $error ['message'], 'Maximum execution time' ) !== false;
	}

	/**
	 * Check whether the job's failure rate is above the specified threshold within the timeframe.
	 *
	 * @param string     $hook The job action hook.
	 * @param array|null $args The job arguments.
	 *
	 * @return bool True if the job's error rate is above the threshold, and false otherwise.
	 *
	 * @see ActionSchedulerJobMonitor::get_failure_rate_threshold()
	 * @see ActionSchedulerJobMonitor::get_failure_timeframe()
	 *
	 * @since 1.7.0
	 */
	protected function is_failure_rate_above_threshold( string $hook, ?array $args = null ): bool {
		$failed_actions = $this->action_scheduler->search(
			[
				'hook'         => $hook,
				'args'         => $args,
				'status'       => $this->action_scheduler::STATUS_FAILED,
				'per_page'     => $this->get_failure_rate_threshold(),
				'date'         => gmdate( 'U' ) - $this->get_failure_timeframe(),
				'date_compare' => '>',
			],
			'ids'
		);

		return count( $failed_actions ) >= $this->get_failure_rate_threshold();
	}

	/**
	 * Get the job failure rate threshold (per timeframe).
	 *
	 * @return int
	 */
	protected function get_failure_rate_threshold(): int {
		return absint( apply_filters( 'woocommerce_gla_job_failure_rate_threshold', 3 ) );
	}

	/**
	 * Get the job failure timeframe (in seconds).
	 *
	 * @return int
	 */
	protected function get_failure_timeframe(): int {
		return absint( apply_filters( 'woocommerce_gla_job_failure_timeframe', 2 * HOUR_IN_SECONDS ) );
	}

	/**
	 * Generates a unique hash (checksum) for each job using its hook name and arguments.
	 *
	 * @param string     $hook
	 * @param array|null $args
	 *
	 * @return string
	 *
	 * @since 1.7.0
	 */
	protected static function get_job_hash( string $hook, ?array $args = null ): string {
		return hash( 'crc32b', $hook . json_encode( $args ) );
	}

	/**
	 * Determines whether the given set of job hook and arguments is monitored for timeout.
	 *
	 * @param string     $hook
	 * @param array|null $args
	 *
	 * @return bool
	 *
	 * @since 1.7.0
	 */
	protected function is_monitored_for_timeout( string $hook, ?array $args = null ): bool {
		return isset( $this->monitored_hooks[ self::get_job_hash( $hook, $args ) ] );
	}
}
