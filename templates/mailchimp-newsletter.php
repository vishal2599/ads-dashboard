<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php

$args = [
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post__not_in' => json_decode(get_option('340_mailchimp_posts_exlude')),
    'post_status' => 'publish'
];

$all_posts = new \WP_Query($args);
$newsletters_in_draft = (empty(json_decode(get_option('340_mailchimp_drafts'))) && empty(json_decode(get_option('340_mailchimp_posts')))) ? false : true;
// echo '<pre>';
//  print_r(json_decode( get_option('340_mailchimp_articles')));
?>
<style>
    .adv-create-ad .form-fields input[type="text"] {
        width: 400px;
    }
</style>
<div class="adv-create-ad wrap 340b-newsletter-posts">
    <h1 class="wp-heading-inline">Edit the NewsLetter Configuration</h1>

    <form class="adv_admin_edit adv-form mailchimp-newsletter" method="post" name='adv_dashboard_edit_form'>
        <div class="advertise-add <?php echo ($newsletters_in_draft) ? ' has-drafts' : ''; ?>">
            <div class="form-fields">
                <h3 class="adv-headings">API key: </h3><br><br>
                <?php $api_key = get_option('340_mailchimp_key'); ?>
                <input type="text" name="340_mailchimp_key" placeholder="Enter Api Key" value="<?php echo ($api_key) ? $api_key : ''; ?>">
            </div>
            <div class="form-fields">
                <h3 class="adv-headings">Newsletter Subject: </h3><br><br>
                <?php $news_subject = get_option('340_mailchimp_subject'); ?>
                <input type="text" name="340_mailchimp_subject" placeholder="Enter email subject" value="<?php echo ($news_subject) ? $news_subject : ''; ?>">
            </div>
            <div class="form-fields">
                <h3 class="adv-headings">Select posts to send in NewsLetter: </h3><br><br>
                <p class="error" style="color:red;display:none;font-size:15px;">No posts selected.</p>
                <select name="newsletter_posts" name="states[]" multiple="multiple" class="newsletter_posts">
                    <?php while ($all_posts->have_posts()) : $all_posts->the_post(); ?>
                        <option value="<?php the_ID(); ?>"><?php echo get_the_title(); ?></option>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </select>
            </div>
            <div class="form-adver additional-article">
                <div class="form-fields">
                    <h3 class="adv-headings">Additional articles for Newsletter </h3>
                </div>
                <div class="form-fields"><input name="article_title[]" type="text" placeholder="Title of article"></div>
                <div class="form-fields"><input name="article_url[]" type="text" placeholder="Article URL"></div>
                <div class="form-fields"><textarea name="article_description[]" placeholder="Article description" spellcheck="false"></textarea></div>
            </div>
            <div class="form-adver">
                <div class="form-fields"><a href="javascript:void(0);" class="add-more-post"><span class="plus"></span>Add another article</a></div>
            </div>
            <div class="form-fields not-visible">
                <input type="hidden" name="action" value="340b_mailchimp_newsletter" />
                <?php $mailchimp_340b_nonce = wp_create_nonce('mailchimp_340b_nonce');  ?>
                <input type="hidden" name="mailchimp_340b_nonce" value="<?php echo $mailchimp_340b_nonce; ?>" />
            </div>
            <input type="submit" value="Create Newsletter" class="button-primary create-newsletter" style="margin-top: 20px;">
        </div>
        <div class="advertise-form">
            <div class="center-form">
                <?php if ($newsletters_in_draft) : ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">Newsletter in Draft: </h3><br><br>
                        <h4><span style="font-size:1.3em;font-weight:400;">Subject:</span> <?php echo get_option('340_mailchimp_subject'); ?></h4>
                    </div>
                    <div class="form-fields">
                        <h4><span style="font-size:1.3em;font-weight:400;">Selected Posts:</span></h4>
                        <?php $drafts = json_decode(get_option('340_mailchimp_posts')); ?>
                        <?php $additional = json_decode(get_option('340_mailchimp_articles')); ?>
                        <ol>
                        <?php if( !empty($additional[0]) ): ?>
                            <li><?php echo $additional[0]->title; ?> <strong>( Additional )</strong></li>
                        <?php endif; ?>
                            <?php foreach ($drafts as $p) : ?>
                                <li><?php echo get_the_title($p); ?></li>
                            <?php endforeach; ?>
                            <?php if( !empty($additional[1]) ): ?>
                                <li><?php echo $additional[1]->title; ?> <strong>( Additional )</strong></li>
                            <?php endif; ?>
                        </ol>
                    </div>
                    <div class="form-fields not-visible">
                        <input type="hidden" name="action" value="340b_mailchimp_newsletter_send" />
                        <?php $mailchimp_340b_send_nonce = wp_create_nonce('mailchimp_340b_send_nonce');  ?>
                        <input type="hidden" name="mailchimp_340b_send_nonce" value="<?php echo $mailchimp_340b_send_nonce; ?>" />
                    </div>
                    <input type="submit" value="Send Newsletter" class="button-primary" style="margin-top: 20px;">
                    <input type="button" value="Remove Draft" class="button-secondary remove-draft" style="margin-top: 20px;margin-left:20px;" data-nonce="<?php echo wp_create_nonce('340b_remove_mailchimp_drafts_nonce'); ?>" data-action="340b_remove_mailchimp_drafts">
                <?php else : ?>
                    <div class="form-fields">
                        <h3 class="adv-headings">No Newsletters in Draft. </h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(document).on('ready', function() {
        $('.340b-newsletter-posts .newsletter_posts').select2({
            placeholder: "Select / Type the post name to send",
        });
    });
</script>