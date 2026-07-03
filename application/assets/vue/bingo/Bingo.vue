<template>
    <div class="bingo" :class="{'is-loading': !initialized}">
        <nav class="bingo__bar">
            <router-link :to="{name: 'app.leaderboard'}" class="bingo__icon-button bingo__icon-button--trophy" title="Scorebord">
                <span class="bingo__icon"></span>
            </router-link>

            <button
                type="button"
                class="bingo__icon-button bingo__icon-button--notifications"
                :class="{'is-active': notificationsEnabled}"
                :title="notificationsEnabled ? 'Notificaties uitzetten' : 'Notificaties aanzetten'"
                @click="toggleNotifications"
            >
                <span class="bingo__icon"></span>
            </button>
        </nav>

        <header class="bingo__header">
            <h1>Mannenweekend Bingo</h1>
        </header>

        <div
            v-if="squares.length"
            class="bingo__grid"
            :style="{'--columns': columns}"
        >
            <div
                v-for="square in squares"
                :key="square.getID()"
                class="bingo__square"
                :class="{'is-completed': isCompleted(square), 'is-disabled': !isAuthenticated}"
                @click="openPopup(square)"
            >
                <span class="bingo__objective">{{ square.getObjective() }}</span>

                <span
                    v-if="completionCount(square) > 1"
                    class="bingo__badge"
                >{{ completionCount(square) }}</span>
            </div>
        </div>

        <p v-else class="bingo__empty">Er zijn nog geen vakjes toegevoegd.</p>

        <section class="bingo__log">
            <p v-if="!squaresCompleted.length" class="bingo__empty">
                Er is nog niets afgevinkt.
            </p>

            <ul v-else class="bingo__log-list">
                <bingo-log-item
                    v-for="completion in squaresCompleted"
                    :key="completion.getID()"
                    :completion="completion"
                    :clickable="true"
                    @open="openCompletion"
                ></bingo-log-item>
            </ul>
        </section>

        <div v-if="activeSquare" class="bingo__overlay" @click.self="closePopup">
            <div class="bingo__popup">
                <header class="bingo__popup-header">
                    <h3>{{ activeSquare.getObjective() }}</h3>
                    <button type="button" class="bingo__popup-close" @click="closePopup">&times;</button>
                </header>

                <label class="bingo__field">
                    <span>Wie deed het?</span>
                    <select v-model="offenderId" class="bingo__select">
                        <option :value="null" disabled>Kies een deelnemer</option>
                        <option
                            v-for="user in users"
                            :key="user.getID()"
                            :value="user.getID()"
                        >{{ user.getName() }}</option>
                    </select>
                </label>

                <label class="bingo__field">
                    <span>Reden</span>
                    <textarea
                        v-model="reason"
                        class="bingo__textarea"
                        rows="3"
                        placeholder="Wat gebeurde er?"
                    ></textarea>
                </label>

                <footer class="bingo__popup-footer">
                    <button type="button" class="bingo__button is-secondary" @click="closePopup">
                        Sluiten
                    </button>
                    <button
                        type="button"
                        class="bingo__button is-primary"
                        :disabled="!offenderId || submitting"
                        @click="submit"
                    >
                        {{ submitting ? 'Bezig…' : 'Afvinken' }}
                    </button>
                </footer>
            </div>
        </div>
    </div>
</template>

<script>

import {enableNotifications, disableNotifications, notificationsSupported} from '../../js/app/notifications';
import BingoLogItem from './BingoLogItem.vue';

/**
 * Bingo component
 *
 * Renders the bingo card and the log of completed squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'Bingo',

    components:
    {
        BingoLogItem
    },

    data()
    {
        return {
            /**
             * @var {boolean} initialized Whether the component has been initialized
            */
            initialized: false,

            /**
             * @var {boolean} togglingNotifications Whether a notification toggle is in progress
            */
            togglingNotifications: false,

            /**
             * @var {SquareRecord|null} activeSquare The square the popup is opened for
            */
            activeSquare: null,

            /**
             * @var {number|null} offenderId The selected offender
            */
            offenderId: null,

            /**
             * @var {string} reason The entered reason
            */
            reason: '',

            /**
             * @var {boolean} submitting Whether a completion is being submitted
            */
            submitting: false
        };
    },

    /**
     * Sets the page title on mount
     * @return {void}
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    mounted()
    {
        this.initialized = true;
    },

    computed:
    {
        /**
         * Returns the number of columns for the grid based on the amount of squares
         * @return {number} The number of columns
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        columns()
        {
            return Math.max(1, Math.ceil(Math.sqrt(this.squares.length)));
        },

        /**
         * Returns the completions grouped by their square identifier
         * @return {Object} A map of square identifier to completions
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        completionsBySquare()
        {
            return this.$store.getters['squaresCompleted/by_square'];
        },

        /**
         * Returns whether the authenticated participant has notifications enabled
         * @return {boolean} True when notifications are enabled
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        notificationsEnabled()
        {
            return this.isAuthenticated && this.currentUser.hasNotifications();
        }
    },

    methods:
    {
        /**
         * Returns the number of completions for the given square
         * @param {SquareRecord} square The square to count completions for
         * @return {number} The number of completions
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        completionCount(square)
        {
            let completions = this.completionsBySquare[square.getID()];

            return completions ? completions.length : 0;
        },

        /**
         * Returns whether the given square has at least one completion
         * @param {SquareRecord} square The square to check
         * @return {boolean} True when the square is completed
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        isCompleted(square)
        {
            return this.completionCount(square) > 0;
        },

        /**
         * Opens the completion popup for the given square
         * @param {SquareRecord} square The square that was tapped
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        openPopup(square)
        {
            if (!this.isAuthenticated) {
                return;
            }

            this.activeSquare = square;
            this.offenderId = null;
            this.reason = '';
        },

        /**
         * Opens the detail page for a completed square
         * @param {SquareCompletedRecord} completion The completion to open
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        openCompletion(completion)
        {
            this.$router.push({name: 'app.bingo.log.detail', params: {id: completion.getID()}});
        },

        /**
         * Turns push notifications on or off for the authenticated participant
         * @return {Promise<void>}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async toggleNotifications()
        {
            if (!this.isAuthenticated) {
                this.openDialogue('Open eerst je persoonlijke link om mee te doen.');
                return;
            }

            if (this.togglingNotifications) {
                return;
            }

            if (!notificationsSupported()) {
                this.openDialogue('Notificaties worden niet ondersteund in deze browser.');
                return;
            }

            this.togglingNotifications = true;

            try {
                if (this.notificationsEnabled) {
                    await disableNotifications();
                    await this.$store.dispatch('currentUser/unsubscribe');
                } else {
                    let endpoint = await enableNotifications(this.$store.getters['currentUser/vapidPublicKey']);
                    await this.$store.dispatch('currentUser/subscribe', endpoint);
                }
            } catch (error) {
                this.openDialogue(error.message || 'Er ging iets mis met de notificaties.');
            } finally {
                this.togglingNotifications = false;
            }
        },

        /**
         * Closes the completion popup
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        closePopup()
        {
            this.activeSquare = null;
        },

        /**
         * Submits the completion to the server and refreshes the store
         * @return {Promise<void>}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async submit()
        {
            if (!this.offenderId || this.submitting) {
                return;
            }

            this.submitting = true;

            try {
                await this.$store.dispatch('squaresCompleted/create', {
                    squareId: this.activeSquare.getID(),
                    offenderId: this.offenderId,
                    reason: this.reason,
                });

                this.closePopup();
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
