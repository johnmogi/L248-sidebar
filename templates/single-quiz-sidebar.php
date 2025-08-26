<?php
/**
 * Template for quizzes with sidebar
 *
 * Based on the LearnDash quiz template but with added sidebar
 *
 * @package Lilac_Quiz_Sidebar
 */

if (!defined('ABSPATH')) {
    exit;
}

// Debug: Log template loading
error_log('Lilac Quiz Sidebar: Loading template file: ' . __FILE__);

// Include theme header
get_header();


// Get quiz ID and settings
$quiz_id = get_the_ID();
$has_sidebar = get_post_meta($quiz_id, '_ld_quiz_toggle_sidebar', true);
$enforce_hint = get_post_meta($quiz_id, '_ld_quiz_enforce_hint', true);

// Debug output removed for production
?>

<script type="text/javascript">
console.log('Lilac Quiz Sidebar: Template loaded');
console.log('Quiz ID:', <?php echo json_encode($quiz_id); ?>);
console.log('Has Sidebar:', <?php echo json_encode($has_sidebar); ?>);
console.log('Enforce Hint:', <?php echo json_encode($enforce_hint); ?>);

jQuery(document).ready(function($) {
    console.log('Lilac Quiz Sidebar: Document ready');
    
    // Debug: Check if our script is loaded
    if (typeof window.lilacQuizSidebarInit === 'function') {
        console.log('Lilac Quiz Sidebar: Main script is loaded');
    } else {
        console.warn('Lilac Quiz Sidebar: Main script not loaded!');
    }
    
    // Debug: Check LearnDash
    if (typeof LearnDashData !== 'undefined') {
        console.log('LearnDash data:', LearnDashData);
    } else {
        console.warn('LearnDashData not found!');
    }
});
</script>

<main id="primary" class="site-main lilac-quiz-main">
    <div class="quiz-container" data-quiz-id="<?php echo esc_attr($quiz_id); ?>" data-has-sidebar="<?php echo esc_attr($has_sidebar); ?>">
        <!-- Main Quiz Content -->
        <div class="quiz-content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('sfwd-quiz lilac-quiz-article'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content quiz-entry-content">
                        <?php 
                        // Output the quiz content using LearnDash shortcode
                        $quiz_content = do_shortcode('[ld_quiz quiz_id="' . $quiz_id . '"]');
                        
                        // Ensure content is properly wrapped
                        if (!empty($quiz_content)) {
                            echo '<div class="lilac-quiz-wrapper">' . $quiz_content . '</div>';
                        } else {
                            echo '<div class="lilac-quiz-error">';
                            echo '<p>' . __('שגיאה בטעינת המבחן. אנא רענן את הדף ונסה שוב.', 'lilac-quiz-sidebar') . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </article>
            <?php endwhile; else: ?>
                <div class="lilac-no-quiz">
                    <p><?php _e('מבחן לא נמצא.', 'lilac-quiz-sidebar'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Enhanced Sidebar -->
        <aside class="ld-quiz-sidebar lilac-enhanced-sidebar" role="complementary" aria-label="<?php esc_attr_e('תוכן עזר למבחן', 'lilac-quiz-sidebar'); ?>">
            <?php 
            // Get the current course ID from the quiz
            $course_id = learndash_get_course_id();
            $course_url = '#';
            
            // If we have a course ID, get its URL
            if (!empty($course_id)) {
                $course_url = get_permalink($course_id);
            }
            ?>
            <div class="lilac-back-to-course-wrapper" style="text-align: center; margin: 15px 0;">
                <a href="#" class="back-to-course-btn" 
                   data-course-url="<?php echo esc_url($course_url); ?>"
                   style="display: inline-block; padding: 12px 25px; background-color: rgba(44, 51, 145, 1); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    בחזרה לקורס
                </a>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const backButton = document.querySelector('.back-to-course-btn');
                    if (backButton) {
                        backButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            let courseUrl = this.getAttribute('data-course-url');
                            
                            // If we don't have a course URL, try to find it from LearnDash data
                            if (courseUrl === '#' && typeof LearnDashData !== 'undefined' && LearnDashData.course) {
                                courseUrl = LearnDashData.course.permalink;
                            }
                            
                            // If we still don't have a URL, try to get it from the breadcrumbs
                            if (courseUrl === '#' || !courseUrl) {
                                const breadcrumbLinks = document.querySelectorAll('.ld-breadcrumbs a');
                                if (breadcrumbLinks.length > 1) {
                                    courseUrl = breadcrumbLinks[0].href;
                                }
                            }
                            
                            // If we have a valid URL, navigate to it
                            if (courseUrl && courseUrl !== '#') {
                                window.location.href = courseUrl;
                            }
                        });
                    }
                });
                </script>
                <style>
                .back-to-course-btn:hover {
                    background-color: rgba(44, 51, 145, 1) !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
                }
                </style>
            </div>
            ?>
            
            <div id="question-media" class="question-media-container">
                <div class="media-loading" style="display: none;">
                    <div class="spinner"></div>
                    <p><?php _e('טוען תוכן...', 'lilac-quiz-sidebar'); ?></p>
                </div>
                
                <div class="media-content question-media-image">
                    <?php 
                    $default_image = plugins_url('/assets/images/default-media.png', dirname(__DIR__));
                    if (file_exists(dirname(__DIR__) . '/assets/images/default-media.png')) {
                        echo '<img src="' . esc_url($default_image) . '" alt="' . esc_attr__('תמונת ברירת מחדל', 'lilac-quiz-sidebar') . '" class="fallback-image">';
                    }
                    ?>
                </div>
                
                <div class="media-placeholder">
                </div>
                
                <div class="media-error" style="display: none;">
                </div>
            </div>
        </aside>
    </div>
</main>

<?php
// Include theme footer
get_footer();
