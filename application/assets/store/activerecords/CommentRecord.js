import ActiveRecord from '@flowtogether-vue/ActiveRecord';

/**
 * CommentRecord class
 *
 * ActiveRecord representation of a comment from the API.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class CommentRecord extends ActiveRecord
{
    /**
     * Returns the identifier of the completed square
     * @return {number|null} The completed square identifier
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getSquareCompletedID()
    {
        return this.get('square_completed_id');
    }

    /**
     * Returns the name of the participant that posted the comment
     * @return {string|null} The participant name
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getUser()
    {
        return this.get('user');
    }

    /**
     * Returns the thumbnail of the participant that posted the comment
     * @return {string|null} The participant thumbnail
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getUserThumbnail()
    {
        return this.get('user_thumbnail');
    }

    /**
     * Returns the moment the comment was posted
     * @return {string|null} The posted timestamp
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getPostedAt()
    {
        return this.get('posted_at');
    }

    /**
     * Returns the posted comment text
     * @return {string|null} The comment text
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getComment()
    {
        return this.get('comment');
    }
}

export default CommentRecord;
