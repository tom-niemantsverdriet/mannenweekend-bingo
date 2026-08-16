/**
 * BaseMixin mixin
 *
 * Provides base functionality for Vue components. Imported globally in app.js
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    computed:
    {
        /**
         * Returns all squares from the VueX store
         * @return {Array} The squares
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        squares()
        {
            return this.$store.getters['squares/all'] ?? [];
        },

        /**
         * Returns all participants from the VueX store
         * @return {Array} The participants
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        users()
        {
            return this.$store.getters['users/all'] ?? [];
        },

        /**
         * Returns all completed squares from the VueX store
         * @return {Array} The completions
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        squaresCompleted()
        {
            return this.$store.getters['squaresCompleted/all'] ?? [];
        },

        /**
         * Returns the authenticated participant
         * @return {UserRecord|null} The participant
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        currentUser()
        {
            return this.$store.getters['currentUser/current'];
        },

        /**
         * Returns whether a participant is authenticated
         * @return {boolean} True when authenticated
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        isAuthenticated()
        {
            return this.$store.getters['currentUser/isAuthenticated'];
        },

        /**
         * Returns the title appendix for the page
         * @return {string} The title appendix
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        title_appendix()
        {
            return 'Mannenweekend Bingo';
        }
    },
}
