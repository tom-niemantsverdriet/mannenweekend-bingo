import ActiveRecord from '@flowtogether-vue/ActiveRecord';

/**
 * SquareCompletedRecord class
 *
 * ActiveRecord representation of a completed square from the API.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareCompletedRecord extends ActiveRecord
{
    /**
     * Returns the moment the square was completed
     * @return {string|null} The completion timestamp
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getCompletedAt()
    {
        return this.get('completed_at');
    }

    /**
     * Returns the identifier of the completed square
     * @return {number|null} The square identifier
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getSquareID()
    {
        return this.get('square_id');
    }

    /**
     * Returns the objective (title) of the completed square
     * @return {string|null} The square objective
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getSquare()
    {
        return this.get('square');
    }

    /**
     * Returns the identifier of the offender
     * @return {number|null} The offender identifier
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getOffenderID()
    {
        return this.get('offender_id');
    }

    /**
     * Returns the name of the offender
     * @return {string|null} The offender name
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getOffender()
    {
        return this.get('offender');
    }

    /**
     * Returns the thumbnail of the offender
     * @return {string|null} The offender thumbnail
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getOffenderThumbnail()
    {
        return this.get('offender_thumbnail');
    }

    /**
     * Returns the name of the participant that posted the completion
     * @return {string|null} The poster name
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getPostedBy()
    {
        return this.get('posted_by');
    }

    /**
     * Returns the reason that was supplied for the completion
     * @return {string|null} The reason
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getReason()
    {
        return this.get('reason');
    }
}

export default SquareCompletedRecord;
