import API from '../API';
import CommentRecord from '../activerecords/CommentRecord';

export default {
    namespaced: true,
    actions:
    {
        /**
         * Loads comments for the given completed square without storing them in Vuex state
         * @param {Object} context The Vuex context
         * @param {number} completedId The completed square identifier
         * @return {Promise<Array>} The loaded comment records
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async loadForCompletion(context, completedId)
        {
            // Fetch records without committing them into module state

            let response = await API().get('comment', {
                params: {
                    square_completed_id: completedId
                }
            });

            // Convert response data to active records

            return response.data.data.map(commentData => {
                let commentRecord = new CommentRecord();
                commentRecord.setData(commentData);

                return commentRecord;
            });
        },

        /**
         * Creates a comment for the given completed square
         * @param {Object} context The Vuex context
         * @param {Object} payload The comment payload
         * @param {number} payload.completedId The completed square identifier
         * @param {string} payload.comment The comment text
         * @return {Promise} The request promise
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        create(context, {completedId, comment})
        {
            return API().post('comment/create', {
                square_completed_id: completedId,
                comment: comment,
            });
        }
    }
}
