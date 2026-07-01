import API from '../API';
import SquareRecord from '../activerecords/SquareRecord';

export default {
    namespaced: true,
    state:
    {
        /**
         * @var {Array|null} squares The loaded squares
        */
        squares: null
    },

    actions:
    {
        /**
         * Initializes the squares module by loading all squares from the API
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async initialize({commit})
        {
            let promise = API().get('square');

            promise.then(response => {
                commit('setSquares', response.data.data);
            });

            return promise;
        }
    },

    mutations:
    {
        /**
         * Injects the squares into the state
         * @param {Object} state The current state
         * @param {Array} squares The squares to set
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setSquares(state, squares)
        {
            let squareRecords = [];

            squares.forEach(squareData => {
                let squareRecord = new SquareRecord();
                squareRecord.setData(squareData);
                squareRecords.push(squareRecord);
            });

            state.squares = squareRecords;
        }
    },

    getters:
    {
        /**
         * Returns all squares sorted by position
         * @param {Object} state The current state
         * @return {Array} The squares
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        all(state)
        {
            if (state.squares === null) {
                return [];
            }

            return [...state.squares].sort((a, b) => a.getPosition() - b.getPosition());
        }
    }
}
