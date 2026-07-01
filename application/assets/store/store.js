import {createStore} from 'vuex';
import squares from './stores/squares';
import users from './stores/users';
import squaresCompleted from './stores/squaresCompleted';
import currentUser from './stores/currentUser';

let store = createStore({
    namespaced: true,
    modules: {
        squares,
        users,
        squaresCompleted,
        currentUser
    },

    actions:
    {
        /**
         * Initializes all Vuex modules
         * @return {Promise} A promise that resolves once every module has loaded
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        initialize()
        {
            let promises = [
                this.dispatch('squares/initialize'),
                this.dispatch('users/initialize'),
                this.dispatch('squaresCompleted/initialize'),
                this.dispatch('currentUser/initialize'),
            ];

            return Promise.all(promises);
        }
    }
});

if (typeof window !== 'undefined') {
    window.vueStore = store;
}

export default store;
