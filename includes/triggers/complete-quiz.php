<?php
/**
 * Complete Quiz
 *
 * @package     AutomatorWP\Integrations\WPLMS\Triggers\Complete_Quiz
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WPLMS_Complete_Quiz extends AutomatorWP_Integration_Trigger {

    public $integration = 'wplms';
    public $trigger = 'wplms_complete_quiz';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a quiz', 'automatorwp-wplms-integration' ),
            'select_option'     => __( 'User <strong>completes</strong> a quiz', 'automatorwp-wplms-integration' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp-wplms-integration' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp-wplms-integration' ), '{post}' ),
            'action'            => 'wplms_submit_quiz',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Quiz:', 'automatorwp-wplms-integration' ),
                    'option_none_label' => __( 'any quiz', 'automatorwp-wplms-integration' ),
                    'post_type' => 'quiz'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param integer $quiz_id
     * @param integer $user_id
     */
    public function listener( $quiz_id, $user_id ) {

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $quiz_id,
            'operation' => 'complete',
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Shorthand
        $operation = $trigger_options['operation'];

        // Ensure operation default value
        if( empty( $operation ) ) {
            $operation = 'complete';
        }

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['operation'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        // Don't deserve if operation doesn't match with the trigger option
        if( $operation !== $event['operation'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WPLMS_Complete_Quiz();