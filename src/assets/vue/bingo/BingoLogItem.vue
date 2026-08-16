<template>
    <li
        class="bingo__log-item"
        :class="{'is-clickable': clickable}"
        @click="open"
    >
        <div class="bingo__log-avatar">
            <img
                v-if="completion.getOffenderThumbnail()"
                :src="completion.getOffenderThumbnail()"
                :alt="completion.getOffender()"
            >
            <span v-else class="bingo__log-initial">
                {{ (completion.getOffender() || '?').charAt(0) }}
            </span>
        </div>
        <div class="bingo__log-body">
            <div class="bingo__log-title">{{ completion.getSquare() }}: <strong>{{ completion.getOffender() }}</strong></div>
            <div class="bingo__log-reason" v-if="completion.getReason()">
                &ldquo;{{ completion.getReason() }}&rdquo;
            </div>
            <div class="bingo__log-meta">
                Gemeld door {{ completion.getPostedBy() }}
                <span class="bingo__log-date" v-if="completion.getCompletedAt()">
                    &middot; {{ format_datetime(completion.getCompletedAt()) }}
                </span>
            </div>
        </div>
        <div class="bingo__log-comments" title="Reacties">
            <span class="bingo__log-comments-icon"></span>
            <span>{{ completion.getCommentCount() }}</span>
        </div>
    </li>
</template>

<script>

/**
 * BingoLogItem component
 *
 * Renders a completed square log item with its comment count.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'BingoLogItem',

    props:
    {
        /**
         * @var {SquareCompletedRecord} completion The completion to render
        */
        completion: {
            type: Object,
            required: true
        },

        /**
         * @var {boolean} clickable Whether the item emits an open event
        */
        clickable: {
            type: Boolean,
            default: false
        }
    },

    emits: ['open'],

    methods:
    {
        /**
         * Emits the open event when the item is clickable
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        open()
        {
            if (!this.clickable) {
                return;
            }

            this.$emit('open', this.completion);
        }
    }
}
</script>
