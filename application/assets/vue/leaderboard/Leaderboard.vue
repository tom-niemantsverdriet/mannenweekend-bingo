<template>
    <div class="leaderboard">
        <nav class="leaderboard__bar">
            <router-link :to="{name: 'app.bingo'}" class="leaderboard__back" title="Terug">
                <span>&larr;</span>
            </router-link>
            <h1 class="leaderboard__title">Scorebord</h1>
        </nav>

        <p v-if="!ranking.length" class="leaderboard__empty">Er zijn nog geen deelnemers.</p>

        <ul v-else class="leaderboard__list">
            <li
                v-for="entry in ranking"
                :key="entry.user.getID()"
                class="leaderboard__card"
                @click="openUser(entry.user)"
            >
                <div class="leaderboard__avatar">
                    <img
                        v-if="entry.user.getThumbnail()"
                        :src="entry.user.getThumbnail()"
                        :alt="entry.user.getName()"
                    >
                    <span v-else class="leaderboard__initial">
                        {{ entry.user.getName().charAt(0) }}
                    </span>
                </div>

                <span class="leaderboard__name">{{ entry.user.getName() }}</span>

                <span class="leaderboard__count">{{ entry.count }}</span>
            </li>
        </ul>
    </div>
</template>

<script>

/**
 * Leaderboard component
 *
 * Lists all participants ordered by the amount of offenses they committed.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'Leaderboard',

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
         * Returns the participants together with their offense count, ordered descending
         * @return {Array} The ranking entries
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        ranking()
        {
            let counts = {};

            this.squaresCompleted.forEach(completion => {
                let offenderId = completion.getOffenderID();
                counts[offenderId] = (counts[offenderId] || 0) + 1;
            });

            return this.users
                .map(user => ({user: user, count: counts[user.getID()] || 0}))
                .sort((a, b) => b.count - a.count);
        }
    },

    methods:
    {
        /**
         * Opens the detail page of the given participant
         * @param {UserRecord} user The participant to open
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        openUser(user)
        {
            this.$router.push({name: 'app.leaderboard.user', params: {id: user.getID()}});
        }
    }
}
</script>

