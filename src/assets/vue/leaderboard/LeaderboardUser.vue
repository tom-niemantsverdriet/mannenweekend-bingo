<template>
    <div class="leaderboard-user">
        <nav class="leaderboard-user__bar">
            <router-link :to="{name: 'app.leaderboard'}" class="leaderboard-user__back" title="Terug">
                <span>&larr;</span>
            </router-link>
            <h1 class="leaderboard-user__title">{{ user ? user.getName() : 'Deelnemer' }}</h1>
        </nav>

        <div v-if="user" class="leaderboard-user__hero">
            <div class="leaderboard-user__avatar">
                <img v-if="user.getThumbnail()" :src="user.getThumbnail()" :alt="user.getName()">
                <span v-else class="leaderboard-user__initial">{{ user.getName().charAt(0) }}</span>
            </div>
            <p class="leaderboard-user__count">{{ offenses.length }} {{ offenses.length === 1 ? 'misdrijf' : 'misdrijven' }}</p>
        </div>

        <p v-if="!offenses.length" class="leaderboard-user__empty">
            Deze deelnemer heeft nog niets op zijn kerfstok.
        </p>

        <ul v-else class="leaderboard-user__list">
            <bingo-log-item
                v-for="offense in offenses"
                :key="offense.getID()"
                :completion="offense"
                :clickable="true"
                @open="openCompletion"
            ></bingo-log-item>
        </ul>
    </div>
</template>

<script>

import BingoLogItem from '../bingo/BingoLogItem.vue';

/**
 * LeaderboardUser component
 *
 * Lists all offenses committed by a single participant.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'LeaderboardUser',

    components:
    {
        BingoLogItem
    },

    /**
     * Sets the page title on mount
     * @return {void}
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    mounted()
    {
    },

    computed:
    {
        /**
         * Returns the identifier of the participant from the route
         * @return {number} The participant identifier
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        userId()
        {
            return Number(this.$route.params.id);
        },

        /**
         * Returns the participant that is being viewed
         * @return {UserRecord|null} The participant
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        user()
        {
            return this.users.find(user => user.getID() === this.userId) ?? null;
        },

        /**
         * Returns all offenses committed by the participant
         * @return {Array} The offenses
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        offenses()
        {
            return this.squaresCompleted.filter(completion => completion.getOffenderID() === this.userId);
        }
    },

    methods:
    {
        /**
         * Opens the detail page for a completed square
         * @param {SquareCompletedRecord} completion The completion to open
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        openCompletion(completion)
        {
            this.$router.push({name: 'app.bingo.log.detail', params: {id: completion.getID()}});
        }
    }
}
</script>
