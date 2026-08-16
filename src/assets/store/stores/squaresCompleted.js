import API from '../API';
import SquareCompletedRecord from '../activerecords/SquareCompletedRecord';

export default {
    namespaced: true,
    state:
    {
        /**
         * @var {Array|null} completions The loaded completions
        */
        completions: null,

        /**
         * @var {number} pollTimestamp The timestamp the long-poll continues from
        */
        pollTimestamp: 0
    },

    actions:
    {
        /**
         * Initializes the module by loading all completed squares from the API
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async initialize({commit})
        {
            let promise = API().get('square-completed');

            promise.then(response => {
                commit('setCompletions', response.data.data);
            });

            return promise;
        },

        /**
         * Registers a new completion on the server and refreshes the store
         * @param {Object} context The Vuex context
         * @param {Object} payload The completion payload
         * @param {number} payload.squareId The identifier of the completed square
         * @param {number} payload.offenderId The identifier of the offender
         * @param {string} payload.reason The reason for the completion
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async create({dispatch}, {squareId, offenderId, reason})
        {
            await API().post('square-completed/create', {
                square_id: squareId,
                offender_id: offenderId,
                reason: reason,
            });

            return dispatch('initialize');
        },

        /**
         * Starts the long-poll loop from the current moment
         * @param {Object} context The Vuex context
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        startPolling({commit, dispatch})
        {
            commit('setPollTimestamp', Math.floor(Date.now() / 1000));
            dispatch('poll');
        },

        /**
         * Performs one long-poll request and schedules the next one. The next poll is only
         * scheduled through requestAnimationFrame, so polling pauses while the tab is inactive.
         * When the returned timestamp is newer than the one we held, the store is refreshed.
         * @param {Object} context The Vuex context
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async poll({state, commit, dispatch})
        {
            let scheduleNext = () => {
                if (typeof requestAnimationFrame === 'function') {
                    requestAnimationFrame(() => dispatch('poll'));
                    return;
                }

                dispatch('poll');
            };

            try {
                let response = await API().post('square-completed/poll', {
                    timestamp: state.pollTimestamp
                });

                let timestamp = response.data.data.timestamp;

                if (timestamp > state.pollTimestamp) {
                    commit('setPollTimestamp', timestamp);
                    await dispatch('initialize');
                }
            } catch (error) {
                // Swallow errors (e.g. a dropped connection) and keep polling
            }

            scheduleNext();
        }
    },

    mutations:
    {
        /**
         * Injects the completions into the state
         * @param {Object} state The current state
         * @param {Array} completions The completions to set
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setCompletions(state, completions)
        {
            let completionRecords = [];

            completions.forEach(completionData => {
                let completionRecord = new SquareCompletedRecord();
                completionRecord.setData(completionData);
                completionRecords.push(completionRecord);
            });

            state.completions = completionRecords;
        },

        /**
         * Stores the timestamp the long-poll should continue from
         * @param {Object} state The current state
         * @param {number} timestamp The timestamp
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        setPollTimestamp(state, timestamp)
        {
            state.pollTimestamp = timestamp;
        }
    },

    getters:
    {
        /**
         * Returns all completions
         * @param {Object} state The current state
         * @return {Array} The completions
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        all(state)
        {
            return state.completions;
        },

        /**
         * Returns the completions grouped by the square identifier they belong to
         * @param {Object} state The current state
         * @return {Object} A map of square identifier to completions
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        by_square(state)
        {
            let map = {};

            if (state.completions === null) {
                return map;
            }

            state.completions.forEach(completion => {
                let squareId = completion.getSquareID();

                if (!map[squareId]) {
                    map[squareId] = [];
                }

                map[squareId].push(completion);
            });

            return map;
        }
    }
}
