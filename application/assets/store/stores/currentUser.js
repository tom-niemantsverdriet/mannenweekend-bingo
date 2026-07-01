import API from '../API';
import UserRecord from '../activerecords/UserRecord';

export default {
    namespaced: true,
    state:
    {
        /**
         * @var {UserRecord|null} user The authenticated participant
        */
        user: null,

        /**
         * @var {string|null} vapidPublicKey The public key used to subscribe to push notifications
        */
        vapidPublicKey: null
    },

    actions:
    {
        /**
         * Loads the authenticated participant and the VAPID public key
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async initialize({commit})
        {
            let promise = API().get('current-user');

            promise.then(response => {
                commit('setUser', response.data.data.user);
                commit('setVapidPublicKey', response.data.data.vapid_public_key);
            });

            return promise;
        },

        /**
         * Stores the given push subscription endpoint on the authenticated participant
         * @param {Object} context The Vuex context
         * @param {string} url The push subscription endpoint URL
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async subscribe({commit}, url)
        {
            let response = await API().post('current-user/subscribe', {url: url});

            commit('setUser', response.data.data);

            return response.data.data;
        },

        /**
         * Clears the push subscription of the authenticated participant
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async unsubscribe({commit})
        {
            let response = await API().post('current-user/unsubscribe');

            commit('setUser', response.data.data);

            return response.data.data;
        }
    },

    mutations:
    {
        /**
         * Injects the authenticated participant into the state
         * @param {Object} state The current state
         * @param {Object|null} userData The user data or null
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setUser(state, userData)
        {
            if (!userData) {
                state.user = null;
                return;
            }

            let userRecord = new UserRecord();
            userRecord.setData(userData);
            state.user = userRecord;
        },

        /**
         * Stores the VAPID public key
         * @param {Object} state The current state
         * @param {string|null} key The VAPID public key
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setVapidPublicKey(state, key)
        {
            state.vapidPublicKey = key;
        }
    },

    getters:
    {
        /**
         * Returns the authenticated participant
         * @param {Object} state The current state
         * @return {UserRecord|null} The participant
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        current(state)
        {
            return state.user;
        },

        /**
         * Returns whether a participant is authenticated
         * @param {Object} state The current state
         * @return {boolean} True when authenticated
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        isAuthenticated(state)
        {
            return state.user !== null;
        },

        /**
         * Returns the VAPID public key
         * @param {Object} state The current state
         * @return {string|null} The key
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        vapidPublicKey(state)
        {
            return state.vapidPublicKey;
        }
    }
}
