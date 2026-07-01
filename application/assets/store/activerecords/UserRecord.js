import ActiveRecord from '@flowtogether-vue/ActiveRecord';

/**
 * UserRecord class
 *
 * ActiveRecord representation of a participant from the API.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class UserRecord extends ActiveRecord
{
    /**
     * Returns the name of the participant
     * @return {string|null} The name
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getName()
    {
        return this.get('name');
    }

    /**
     * Returns the thumbnail URL of the participant
     * @return {string|null} The thumbnail
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getThumbnail()
    {
        return this.get('thumbnail');
    }

    /**
     * Returns whether the participant has notifications enabled
     * @return {boolean} True when notifications are enabled
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    hasNotifications()
    {
        return this.get('notifications_enabled') === true;
    }

    /**
     * Returns the data used for persisting the participant
     * @return {Object} The mapped data
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getData()
    {
        return {
            id: this.getID(),
            name: this.getName(),
            thumbnail: this.getThumbnail(),
        };
    }
}

export default UserRecord;
