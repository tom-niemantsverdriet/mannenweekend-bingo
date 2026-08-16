<template>
    <div class="bingo-detail">
        <nav class="bingo-detail__bar">
            <button type="button" class="bingo-detail__back" title="Terug" @click="goBack">
                <span>&larr;</span>
            </button>
            <h1 class="bingo-detail__title">Reacties</h1>
        </nav>

        <ul v-if="completion" class="bingo__log-list bingo-detail__completion">
            <bingo-log-item :completion="completion"></bingo-log-item>
        </ul>

        <form v-if="isAuthenticated" class="bingo-detail__form" @submit.prevent="submitComment">
            <input
                v-model="comment"
                class="bingo-detail__input"
                type="text"
                placeholder="Schrijf een reactie"
            >
            <button
                type="submit"
                class="bingo-detail__submit"
                :disabled="!canSubmitComment"
            >
                Plaatsen
            </button>
        </form>

        <p v-if="loading" class="bingo-detail__empty">Reacties laden...</p>
        <p v-else-if="!comments.length" class="bingo-detail__empty">Nog geen reacties.</p>

        <ul v-else class="bingo-detail__comments">
            <li
                v-for="commentRecord in comments"
                :key="commentRecord.getID()"
                class="bingo-detail__comment"
            >
                <div class="bingo-detail__comment-avatar">
                    <img
                        v-if="commentRecord.getUserThumbnail()"
                        :src="commentRecord.getUserThumbnail()"
                        :alt="commentRecord.getUser()"
                    >
                    <span v-else>{{ (commentRecord.getUser() || '?').charAt(0) }}</span>
                </div>
                <div class="bingo-detail__comment-body">
                    <div class="bingo-detail__comment-header">
                        <strong>{{ commentRecord.getUser() }}</strong>
                        <span v-if="commentRecord.getPostedAt()">
                            {{ format_datetime(commentRecord.getPostedAt()) }}
                        </span>
                    </div>
                    <p>{{ commentRecord.getComment() }}</p>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>

import BingoLogItem from './BingoLogItem.vue';

/**
 * BingoLogDetail component
 *
 * Shows a completed square with comments that are loaded on demand.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
export default
{
    name: 'BingoLogDetail',

    components:
    {
        BingoLogItem
    },

    data()
    {
        return {
            /**
             * @var {Array} comments The comments loaded for this completed square
            */
            comments: [],

            /**
             * @var {string} comment The comment input value
            */
            comment: '',

            /**
             * @var {boolean} loading Whether comments are loading
            */
            loading: true,

            /**
             * @var {boolean} submitting Whether a comment is being submitted
            */
            submitting: false
        };
    },

    /**
     * Loads the comments after the detail page is mounted
     * @return {void}
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    mounted()
    {
        this.loadComments();
    },

    watch:
    {
        /**
         * Reloads comments when another completed square is opened in this component
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        completionId()
        {
            this.loadComments();
        }
    },

    computed:
    {
        /**
         * Returns the completed square identifier from the route
         * @return {number} The completed square identifier
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        completionId()
        {
            return Number(this.$route.params.id);
        },

        /**
         * Returns the completed square that is being viewed
         * @return {SquareCompletedRecord|null} The completion
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        completion()
        {
            return this.squaresCompleted.find(completion => completion.getID() === this.completionId) ?? null;
        },

        /**
         * Returns whether the current comment can be submitted
         * @return {boolean} True when the button may be enabled
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        canSubmitComment()
        {
            return this.comment.trim() !== '' && !this.submitting;
        }
    },

    methods:
    {
        /**
         * Navigates back in browser history
         * @return {void}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        goBack()
        {
            this.$router.back();
        },

        /**
         * Loads comments from the store without committing them to Vuex state
         * @return {Promise<void>}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async loadComments()
        {
            this.loading = true;

            try {
                this.comments = await this.$store.dispatch('comments/loadForCompletion', this.completionId);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Submits a comment and reloads comments and comment counts
         * @return {Promise<void>}
         * @author Tom Niemantsverdriet <tom@lumitec.nl>
         */
        async submitComment()
        {
            if (!this.canSubmitComment || !this.isAuthenticated) {
                return;
            }

            this.submitting = true;

            try {
                // Store the comment on the server

                await this.$store.dispatch('comments/create', {
                    completedId: this.completionId,
                    comment: this.comment,
                });

                // Refresh local comments and global comment counts

                this.comment = '';
                await this.loadComments();
                await this.$store.dispatch('squaresCompleted/initialize');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
