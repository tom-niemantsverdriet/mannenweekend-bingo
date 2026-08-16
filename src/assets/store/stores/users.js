import API from '../API';
import UserRecord from '../activerecords/UserRecord';

export default {
    namespaced: true,
    state:
    {
        /**
         * @var {Array|null} users The loaded participants
        */
        users: null
    },

    actions:
    {
        /**
         * Initializes the users module by loading all participants from the API
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async initialize({commit})
        {
            let promise = API().get('user');

            promise.then(response => {
                commit('setUsers', response.data.data);
            });

            return promise;
        }
    },

    mutations:
    {
        /**
         * Injects the participants into the state
         * @param {Object} state The current state
         * @param {Array} users The participants to set
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setUsers(state, users)
        {
            let userRecords = [];

            users.forEach(userData => {
                let userRecord = new UserRecord();
                userRecord.setData(userData);
                userRecords.push(userRecord);
            });

            state.users = userRecords;
        }
    },

    getters:
    {
        /**
         * Returns all participants
         * @param {Object} state The current state
         * @return {Array} The participants
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        all(state)
        {
            return state.users;
        }
    }
}
