<?php
// Path: wp-content/plugins/wp2/src/Helpers/Event/init-action-scheduler.php

namespace WP2\Helpers\Event\ActionScheduler;

class Controller
{

    /**
     * Textdomain for translations.
     *
     * @var string
     */
    private static $textdomain = 'wp2';
    /**
     * Register an asynchronous action (single or recurring).
     *
     * @param string                   $hook         The hook name to schedule.
     * @param callable|string|null     $callback     Optional callback to be added to the hook
     *                                              (can be a closure or a global function name).
     * @param array                    $args         Arguments to pass to the action.
     * @param int|null                 $interval     If null, a single action is scheduled;
     *                                              if integer, a recurring action is scheduled using this interval (in seconds).
     * @param bool                     $unique       Whether to skip scheduling if this action is already scheduled.
     * @param array                    $admin_notice {
     *      Optional admin notice parameters:
     *
     *      @type string $type            The notice type (e.g. 'info', 'warning', 'error', 'success').
     *      @type string $message         The notice message text.
     *      @type bool   $is_dismissible  Whether the notice can be dismissed by the user.
     * }
     */
    public static function register_async_action(
        string $hook,
        ?callable $callback = null,
        array $args = [],
        ?int $interval = null,
        bool $unique = true,
        array $admin_notice = []
    ) {
        // 1. Check if Action Scheduler is available.
        if (! self::is_action_scheduler_available()) {
            error_log('Action Scheduler is not available; cannot schedule action: ' . $hook);
            return;
        }

        // 2. If a callback was provided, verify it's callable and add it to the hook.
        if (null !== $callback) {
            if (! is_callable($callback)) {
                // Log or handle the error, then bail out if you prefer.
                error_log("Invalid callback for hook '{$hook}': Not callable.");
                return;
            }
            add_action($hook, $callback);
        }

        // 3. If $unique is true, check if an action is already scheduled with the same hook + args.
        if ($unique && as_next_scheduled_action($hook, $args)) {
            // An identical action is already scheduled; skip scheduling another.
            return;
        }

        // 4. Schedule the action (single or recurring).
        if ($interval !== null && $interval > 0) {
            // Schedule a recurring action.
            as_schedule_recurring_action(
                time(),
                $interval,
                $hook,
                $args
            );
        } else {
            // Schedule a one-time (single) action.
            as_schedule_single_action(
                time(),
                $hook,
                $args
            );
        }

        // 5. Optional admin notice (so the user knows a task was just scheduled).
        self::show_admin_notice(
            $admin_notice['type']          ?? 'info',
            $admin_notice['message']       ?? '',
            $admin_notice['is_dismissible'] ?? true
        );
    }

    /**
     * Check if Action Scheduler functions are available.
     *
     * @return bool
     */
    private static function is_action_scheduler_available(): bool
    {
        return function_exists('as_next_scheduled_action')
            && function_exists('as_schedule_single_action')
            && function_exists('as_schedule_recurring_action');
    }

    /**
     * Display an admin notice after scheduling the async task.
     *
     * @return void
     */
    private static function show_admin_notice(
        string $type = 'info',
        string $message = '',
        bool $is_dismissible = true
    ) {

        $args = [
            'type' => $type,
            'message' => $message,
            'is_dismissible' => $is_dismissible,
        ];

        add_action('admin_notices', function () use ($args) {
            echo self::get_admin_notice_html($args);
        });
    }

    /**
     * Get the HTML for the admin notice.
     *
     * @param string $type
     * @param string $message
     * @param bool   $is_dismissible
     * @return string
     */
    private static function get_admin_notice_html(array $args): string
    {
        $type           = $args['type'] ?? 'info';
        $message        = $args['message'] ?? '';
        $is_dismissible = $args['is_dismissible'] ?? true;

        $classes = [
            'notice',
            'notice-' . $type,
            $is_dismissible ? 'is-dismissible' : '',
        ];
        $classes = array_map('sanitize_html_class', $classes);

        if (!$message) {
            $message = __('Async task has been scheduled.', self::$textdomain);
        }

        $markup = sprintf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr(implode(' ', $classes)),
            esc_html($message)
        );

        return $markup;
    }
}
add_action('init', function () {
    new Controller();
}, 30);
