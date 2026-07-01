import ActiveRecord from '@flowtogether-vue/ActiveRecord';

/**
 * SquareRecord class
 *
 * ActiveRecord representation of a bingo square from the API.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareRecord extends ActiveRecord
{
    /**
     * Returns the objective (the text on the square)
     * @return {string|null} The objective
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getObjective()
    {
        return this.get('objective');
    }

    /**
     * Returns the position of the square on the card
     * @return {number} The position
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getPosition()
    {
        return this.get('position') ?? 0;
    }

    /**
     * Returns the data used for persisting the square
     * @return {Object} The mapped data
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    getData()
    {
        return {
            id: this.getID(),
            objective: this.getObjective(),
            position: this.getPosition(),
        };
    }
}

export default SquareRecord;
