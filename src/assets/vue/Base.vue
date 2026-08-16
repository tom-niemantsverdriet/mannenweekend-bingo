<template>
    <message-dialogue ref="messageDialogue"></message-dialogue>
    <div id="content">
        <div id="content-inner" v-if="initialized">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>

/**
 * Base component
 *
 * Root component that boots the store and renders the router view. This app does
 * not use the main menu component.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'App',

    data() {
        return {
            /**
             * @var {boolean} initialized Whether the application has been initialized
            */
            initialized: false
        }
    },

    /**
     * Boots the store once the component is mounted
     * @return {void}
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    mounted()
    {
        this.$store.dispatch('initialize').then(() => {
            this.initialized = true;

            if (typeof SmoothFlow !== 'undefined') {
                SmoothFlow.getInstance().setDisabled(true);
            }

            this.$store.dispatch('squaresCompleted/startPolling');
        });
    },

    methods:
    {
        /**
         * Opens the root message dialogue
         * @param {string|object} configuration The message string or configuration object
         * @return {Promise} The dialogue promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        openMessageDialogue(configuration)
        {
            return this.$refs.messageDialogue.open(configuration);
        }
    }
}
</script>
