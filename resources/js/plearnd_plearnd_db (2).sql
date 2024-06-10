-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 04, 2024 at 08:31 AM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plearnd_plearnd_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academies`
--

DROP TABLE IF EXISTS `academies`;
CREATE TABLE IF NOT EXISTS `academies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `slogan` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `director` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `established_year` smallint UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accreditation` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accreditation_body` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `total_students` int UNSIGNED NOT NULL DEFAULT '0',
  `total_teachers` int UNSIGNED NOT NULL DEFAULT '0',
  `membership_fees_points` int UNSIGNED NOT NULL DEFAULT '0',
  `courses_offered` int DEFAULT '0',
  `facilities` text COLLATE utf8mb3_unicode_ci,
  `academy_timings` text COLLATE utf8mb3_unicode_ci,
  `holidays` text COLLATE utf8mb3_unicode_ci,
  `social_media_links` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `academies_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `academies`
--

INSERT INTO `academies` (`id`, `user_id`, `name`, `slogan`, `address`, `email`, `phone`, `director`, `established_year`, `type`, `accreditation`, `accreditation_body`, `total_students`, `total_teachers`, `membership_fees_points`, `courses_offered`, `facilities`, `academy_timings`, `holidays`, `social_media_links`, `logo`, `cover`, `created_at`, `updated_at`) VALUES
(1, 1, 'เพลินวิทยาธาร', 'สายธารวิทยา แหล่งปัญญาไร้พรมแดน', '16 หมู่ 5 ต.ตะโหมด อ.ตะโหมด จ.พัทลุง', NULL, NULL, '1', NULL, NULL, NULL, NULL, 234, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2023-12-14 17:19:19', '2024-05-04 06:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `academy_admins`
--

DROP TABLE IF EXISTS `academy_admins`;
CREATE TABLE IF NOT EXISTS `academy_admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_admins_user_id_foreign` (`user_id`),
  KEY `academy_admins_academy_id_foreign` (`academy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_groups`
--

DROP TABLE IF EXISTS `academy_groups`;
CREATE TABLE IF NOT EXISTS `academy_groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_group_admins`
--

DROP TABLE IF EXISTS `academy_group_admins`;
CREATE TABLE IF NOT EXISTS `academy_group_admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_group_members`
--

DROP TABLE IF EXISTS `academy_group_members`;
CREATE TABLE IF NOT EXISTS `academy_group_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_members`
--

DROP TABLE IF EXISTS `academy_members`;
CREATE TABLE IF NOT EXISTS `academy_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `academy_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `member_code` int UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `role` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `enrollment_date` date NOT NULL DEFAULT '2024-05-04',
  `graduation_date` date DEFAULT NULL,
  `graduation_reason` enum('completed','expelled','dropped_out','transferred','other') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `note_comment` text COLLATE utf8mb3_unicode_ci,
  `additional_info` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `academy_members`
--

INSERT INTO `academy_members` (`id`, `academy_id`, `user_id`, `member_code`, `status`, `role`, `enrollment_date`, `graduation_date`, `graduation_reason`, `note_comment`, `additional_info`, `created_at`, `updated_at`) VALUES
(1, 1, 2, NULL, 1, NULL, '2024-05-04', NULL, NULL, NULL, NULL, '2024-05-04 06:40:19', '2024-05-04 06:40:19');

-- --------------------------------------------------------

--
-- Table structure for table `academy_posts`
--

DROP TABLE IF EXISTS `academy_posts`;
CREATE TABLE IF NOT EXISTS `academy_posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '3',
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `engagement_rate` double NOT NULL DEFAULT '0',
  `post_type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `source_platform` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `interacted_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_posts_user_id_foreign` (`user_id`),
  KEY `academy_posts_academy_id_foreign` (`academy_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_comments`
--

DROP TABLE IF EXISTS `academy_post_comments`;
CREATE TABLE IF NOT EXISTS `academy_post_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `academy_post_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `parent_comment_id` bigint UNSIGNED DEFAULT NULL,
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_comments_academy_post_id_foreign` (`academy_post_id`),
  KEY `academy_post_comments_user_id_foreign` (`user_id`),
  KEY `academy_post_comments_parent_comment_id_foreign` (`parent_comment_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_comment_images`
--

DROP TABLE IF EXISTS `academy_post_comment_images`;
CREATE TABLE IF NOT EXISTS `academy_post_comment_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_dislikes`
--

DROP TABLE IF EXISTS `academy_post_dislikes`;
CREATE TABLE IF NOT EXISTS `academy_post_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_dislikes_user_id_foreign` (`user_id`),
  KEY `academy_post_dislikes_academy_post_id_foreign` (`academy_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_images`
--

DROP TABLE IF EXISTS `academy_post_images`;
CREATE TABLE IF NOT EXISTS `academy_post_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `academy_post_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb3_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_images_academy_post_id_foreign` (`academy_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_image_comments`
--

DROP TABLE IF EXISTS `academy_post_image_comments`;
CREATE TABLE IF NOT EXISTS `academy_post_image_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_post_image_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_image_comments_user_id_foreign` (`user_id`),
  KEY `academy_post_image_comments_academy_post_image_id_foreign` (`academy_post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_image_dislikes`
--

DROP TABLE IF EXISTS `academy_post_image_dislikes`;
CREATE TABLE IF NOT EXISTS `academy_post_image_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_image_dislikes_user_id_foreign` (`user_id`),
  KEY `academy_post_image_dislikes_post_image_id_foreign` (`post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_image_likes`
--

DROP TABLE IF EXISTS `academy_post_image_likes`;
CREATE TABLE IF NOT EXISTS `academy_post_image_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_image_likes_user_id_foreign` (`user_id`),
  KEY `academy_post_image_likes_post_image_id_foreign` (`post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_post_likes`
--

DROP TABLE IF EXISTS `academy_post_likes`;
CREATE TABLE IF NOT EXISTS `academy_post_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_post_likes_user_id_foreign` (`user_id`),
  KEY `academy_post_likes_academy_post_id_foreign` (`academy_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academy_settings`
--

DROP TABLE IF EXISTS `academy_settings`;
CREATE TABLE IF NOT EXISTS `academy_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `academy_id` bigint UNSIGNED NOT NULL,
  `auto_accept_members` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academy_settings_academy_id_foreign` (`academy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `academy_settings`
--

INSERT INTO `academy_settings` (`id`, `academy_id`, `auto_accept_members`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2023-12-14 17:19:19', '2023-12-14 17:19:19');

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `activityable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `activityable_id` bigint NOT NULL,
  `activity_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `action` text COLLATE utf8mb3_unicode_ci,
  `activity_details` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_user_id_foreign` (`user_id`),
  KEY `activities_activityable_type_activityable_id_index` (`activityable_type`,`activityable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignmentable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `assignmentable_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `due_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `points` int NOT NULL DEFAULT '0',
  `increase_points` int DEFAULT '0',
  `decrease_points` int DEFAULT '0',
  `assignment_type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `submission_method` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `max_file_size` int DEFAULT NULL,
  `is_group_assignment` tinyint(1) NOT NULL DEFAULT '0',
  `target_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `grading_rubric` text COLLATE utf8mb3_unicode_ci,
  `graded_score` int DEFAULT NULL,
  `feedback` text COLLATE utf8mb3_unicode_ci,
  `status` tinyint DEFAULT '1' COMMENT '[0=>not_started, 1=> ''published'', 2=> ''in_progress'', 3=> ''submitted'', 4=> ''graded]',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_assignmentable_type_assignmentable_id_index` (`assignmentable_type`,`assignmentable_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_answers`
--

DROP TABLE IF EXISTS `assignment_answers`;
CREATE TABLE IF NOT EXISTS `assignment_answers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci,
  `points` int DEFAULT NULL,
  `submission_date` datetime DEFAULT NULL,
  `attachment_path` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` enum('submitted','in_review','graded') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'submitted',
  `late_submission` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_answers_user_id_foreign` (`user_id`),
  KEY `assignment_answers_assignment_id_foreign` (`assignment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_answer_images`
--

DROP TABLE IF EXISTS `assignment_answer_images`;
CREATE TABLE IF NOT EXISTS `assignment_answer_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_answer_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_answer_images_assignment_answer_id_foreign` (`assignment_answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_images`
--

DROP TABLE IF EXISTS `assignment_images`;
CREATE TABLE IF NOT EXISTS `assignment_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_images_assignment_id_foreign` (`assignment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_details`
--

DROP TABLE IF EXISTS `attendance_details`;
CREATE TABLE IF NOT EXISTS `attendance_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attendanceable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `attendanceable_id` bigint UNSIGNED NOT NULL,
  `course_attendance_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `course_member_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `comments` text COLLATE utf8mb3_unicode_ci,
  `late_threshold` int UNSIGNED DEFAULT NULL,
  `excused_absence_reason` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_details_attendanceable_type_attendanceable_id_index` (`attendanceable_type`,`attendanceable_id`),
  KEY `attendance_details_course_attendance_id_foreign` (`course_attendance_id`),
  KEY `attendance_details_course_id_foreign` (`course_id`),
  KEY `attendance_details_group_id_foreign` (`group_id`),
  KEY `attendance_details_course_member_id_foreign` (`course_member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

DROP TABLE IF EXISTS `communities`;
CREATE TABLE IF NOT EXISTS `communities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `creater_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `creator_id` bigint UNSIGNED NOT NULL,
  `creation_date` timestamp NULL DEFAULT NULL,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'public',
  `category` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `member_count` bigint UNSIGNED NOT NULL DEFAULT '0',
  `rules` text COLLATE utf8mb3_unicode_ci,
  `community_picture` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communities_creator_id_foreign` (`creator_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `community_memberships`
--

DROP TABLE IF EXISTS `community_memberships`;
CREATE TABLE IF NOT EXISTS `community_memberships` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `community_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'member',
  `join_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `last_active` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `community_memberships_community_id_foreign` (`community_id`),
  KEY `community_memberships_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `academy_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `duration` smallint UNSIGNED DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `tuition_fees` int DEFAULT NULL,
  `price` int DEFAULT NULL,
  `discount` int DEFAULT '0',
  `points` bigint DEFAULT '0',
  `credit_units` tinyint DEFAULT '0',
  `hours_per_week` tinyint DEFAULT '1',
  `category` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `capacity` int UNSIGNED DEFAULT NULL,
  `enrolled_students` int UNSIGNED NOT NULL DEFAULT '0',
  `lessons` int DEFAULT '0',
  `assignments` int DEFAULT '0',
  `quizzes` int DEFAULT '0',
  `groups` int DEFAULT '0',
  `class_schedule` text COLLATE utf8mb3_unicode_ci,
  `prerequisites` text COLLATE utf8mb3_unicode_ci,
  `course_materials` text COLLATE utf8mb3_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '[ 1 => ''Active'', 2 => ''Draft'', 3 => ''Completed'', 4 => ''Closed'', ]',
  `saleable` tinyint(1) DEFAULT NULL,
  `accreditation` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accreditation_body` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rating` double(2,1) DEFAULT NULL,
  `cover` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `syllabus` text COLLATE utf8mb3_unicode_ci,
  `total_score` int DEFAULT '0',
  `certificate` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_user_id_foreign` (`user_id`),
  KEY `courses_instructor_id_foreign` (`instructor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_admins`
--

DROP TABLE IF EXISTS `course_admins`;
CREATE TABLE IF NOT EXISTS `course_admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_attendances`
--

DROP TABLE IF EXISTS `course_attendances`;
CREATE TABLE IF NOT EXISTS `course_attendances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `instuctor_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_attendances_course_id_foreign` (`course_id`),
  KEY `course_attendances_group_id_foreign` (`group_id`),
  KEY `course_attendances_instuctor_id_foreign` (`instuctor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_groups`
--

DROP TABLE IF EXISTS `course_groups`;
CREATE TABLE IF NOT EXISTS `course_groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 =''normal'', 1=''closed''',
  `auto_accept_member` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_groups_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_group_admins`
--

DROP TABLE IF EXISTS `course_group_admins`;
CREATE TABLE IF NOT EXISTS `course_group_admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_group_members`
--

DROP TABLE IF EXISTS `course_group_members`;
CREATE TABLE IF NOT EXISTS `course_group_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` enum('0','1') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `last_accessed_tab` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_group_members_course_id_foreign` (`course_id`),
  KEY `course_group_members_group_id_foreign` (`group_id`),
  KEY `course_group_members_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_members`
--

DROP TABLE IF EXISTS `course_members`;
CREATE TABLE IF NOT EXISTS `course_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `member_name` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `course_member_status` tinyint DEFAULT '1',
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `group_member_status` tinyint DEFAULT '1',
  `enrollment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT '0' COMMENT '''0'',''1'',''2'',''3'',''4'',''5''',
  `role` tinyint DEFAULT '1' COMMENT '1=>''student'',2=>''student_leader'',3=>''teacher'',4=>''admin''',
  `order_number` int DEFAULT '0',
  `member_code` int DEFAULT NULL,
  `achieved_score` int DEFAULT '0',
  `bonus_points` int DEFAULT '0',
  `grade_progress` int DEFAULT '0',
  `member_status` tinyint DEFAULT NULL COMMENT '''0'',''1'',''2'',''3'',''4'',''5''',
  `completion_date` timestamp NULL DEFAULT NULL,
  `access_expiry_date` timestamp NULL DEFAULT NULL,
  `notes_comments` text COLLATE utf8mb3_unicode_ci,
  `last_accessed_tab` tinyint DEFAULT '0',
  `last_accessed_group_tab` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_members_user_id_foreign` (`user_id`),
  KEY `course_members_course_id_foreign` (`course_id`),
  KEY `course_members_group_id_foreign` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_posts`
--

DROP TABLE IF EXISTS `course_posts`;
CREATE TABLE IF NOT EXISTS `course_posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_id` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb3_unicode_ci,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '3',
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `engagement_rate` double NOT NULL DEFAULT '0',
  `post_type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `source_platform` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `interacted_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_posts_user_id_foreign` (`user_id`),
  KEY `course_posts_academy_id_foreign` (`academy_id`),
  KEY `course_posts_course_id_foreign` (`course_id`),
  KEY `course_posts_group_id_foreign` (`group_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_comments`
--

DROP TABLE IF EXISTS `course_post_comments`;
CREATE TABLE IF NOT EXISTS `course_post_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_post_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_post_comment_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb3_unicode_ci,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_comments_course_post_id_foreign` (`course_post_id`),
  KEY `course_post_comments_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_comment_dislikes`
--

DROP TABLE IF EXISTS `course_post_comment_dislikes`;
CREATE TABLE IF NOT EXISTS `course_post_comment_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_comment_dislikes_user_id_foreign` (`user_id`),
  KEY `course_post_comment_dislikes_course_post_comment_id_foreign` (`course_post_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_comment_images`
--

DROP TABLE IF EXISTS `course_post_comment_images`;
CREATE TABLE IF NOT EXISTS `course_post_comment_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_post_id` bigint UNSIGNED NOT NULL,
  `post_comment_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_comment_images_course_post_id_foreign` (`course_post_id`),
  KEY `course_post_comment_images_post_comment_id_foreign` (`post_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_comment_likes`
--

DROP TABLE IF EXISTS `course_post_comment_likes`;
CREATE TABLE IF NOT EXISTS `course_post_comment_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_comment_likes_user_id_foreign` (`user_id`),
  KEY `course_post_comment_likes_course_post_comment_id_foreign` (`course_post_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_dislikes`
--

DROP TABLE IF EXISTS `course_post_dislikes`;
CREATE TABLE IF NOT EXISTS `course_post_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_dislikes_user_id_foreign` (`user_id`),
  KEY `course_post_dislikes_course_post_id_foreign` (`course_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_images`
--

DROP TABLE IF EXISTS `course_post_images`;
CREATE TABLE IF NOT EXISTS `course_post_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_post_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb3_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_images_course_post_id_foreign` (`course_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_image_comments`
--

DROP TABLE IF EXISTS `course_post_image_comments`;
CREATE TABLE IF NOT EXISTS `course_post_image_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_image_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_image_comments_user_id_foreign` (`user_id`),
  KEY `course_post_image_comments_course_post_image_id_foreign` (`course_post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_image_comment_dislikes`
--

DROP TABLE IF EXISTS `course_post_image_comment_dislikes`;
CREATE TABLE IF NOT EXISTS `course_post_image_comment_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_image_comment_dislikes_user_id_foreign` (`user_id`),
  KEY `course_post_image_comment_dislikes_comment_id_foreign` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_image_comment_likes`
--

DROP TABLE IF EXISTS `course_post_image_comment_likes`;
CREATE TABLE IF NOT EXISTS `course_post_image_comment_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_image_comment_likes_user_id_foreign` (`user_id`),
  KEY `course_post_image_comment_likes_comment_id_foreign` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_image_dislikes`
--

DROP TABLE IF EXISTS `course_post_image_dislikes`;
CREATE TABLE IF NOT EXISTS `course_post_image_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_image_dislikes_user_id_foreign` (`user_id`),
  KEY `course_post_image_dislikes_course_post_image_id_foreign` (`course_post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_image_likes`
--

DROP TABLE IF EXISTS `course_post_image_likes`;
CREATE TABLE IF NOT EXISTS `course_post_image_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_image_likes_user_id_foreign` (`user_id`),
  KEY `course_post_image_likes_course_post_image_id_foreign` (`course_post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_post_likes`
--

DROP TABLE IF EXISTS `course_post_likes`;
CREATE TABLE IF NOT EXISTS `course_post_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_post_likes_user_id_foreign` (`user_id`),
  KEY `course_post_likes_course_post_id_foreign` (`course_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_quizzes`
--

DROP TABLE IF EXISTS `course_quizzes`;
CREATE TABLE IF NOT EXISTS `course_quizzes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `total_score` int DEFAULT '0',
  `total_questions` int NOT NULL DEFAULT '0',
  `passing_score` int DEFAULT NULL,
  `shuffle_questions` tinyint(1) DEFAULT '0',
  `time_limit` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_quizzes_user_id_foreign` (`user_id`),
  KEY `course_quizzes_course_id_foreign` (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_quiz_results`
--

DROP TABLE IF EXISTS `course_quiz_results`;
CREATE TABLE IF NOT EXISTS `course_quiz_results` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `quiz_id` bigint UNSIGNED NOT NULL,
  `score` int DEFAULT NULL,
  `percentage` double NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `attempted_questions` int DEFAULT NULL,
  `correct_answers` int DEFAULT NULL,
  `incorrect_answers` int DEFAULT NULL,
  `skipped_questions` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_settings`
--

DROP TABLE IF EXISTS `course_settings`;
CREATE TABLE IF NOT EXISTS `course_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `auto_accept_members` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `course_settings_course_id_foreign` (`course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disliked_posts`
--

DROP TABLE IF EXISTS `disliked_posts`;
CREATE TABLE IF NOT EXISTS `disliked_posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disliked_posts_user_id_foreign` (`user_id`),
  KEY `disliked_posts_post_id_foreign` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disliked_post_comments`
--

DROP TABLE IF EXISTS `disliked_post_comments`;
CREATE TABLE IF NOT EXISTS `disliked_post_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disliked_post_comments_user_id_foreign` (`user_id`),
  KEY `disliked_post_comments_post_comment_id_foreign` (`post_comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donates`
--

DROP TABLE IF EXISTS `donates`;
CREATE TABLE IF NOT EXISTS `donates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `donor_id` int DEFAULT NULL,
  `donor_name` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amounts` decimal(10,2) NOT NULL,
  `slip` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `transfer_date` date NOT NULL,
  `transfer_time` time DEFAULT NULL,
  `donor_email` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `donation_purpose` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `remaining_points` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `approved_by` tinyint DEFAULT NULL,
  `notes` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donate_recipients`
--

DROP TABLE IF EXISTS `donate_recipients`;
CREATE TABLE IF NOT EXISTS `donate_recipients` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `donor_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donate_recipients_donor_id_foreign` (`donor_id`),
  KEY `donate_recipients_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE IF NOT EXISTS `friends` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `friend_id` bigint UNSIGNED NOT NULL,
  `status` tinyint NOT NULL,
  `action_user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `friends_user_id_foreign` (`user_id`),
  KEY `friends_friend_id_foreign` (`friend_id`),
  KEY `friends_action_user_id_foreign` (`action_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendships`
--

DROP TABLE IF EXISTS `friendships`;
CREATE TABLE IF NOT EXISTS `friendships` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `recipient_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `recipient_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'pending/accepted/denied/blocked/',
  `action_user_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `friendships_sender_type_sender_id_index` (`sender_type`,`sender_id`),
  KEY `friendships_recipient_type_recipient_id_index` (`recipient_type`,`recipient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendship_groups`
--

DROP TABLE IF EXISTS `friendship_groups`;
CREATE TABLE IF NOT EXISTS `friendship_groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `friendship_id` bigint UNSIGNED NOT NULL,
  `friend_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `friend_id` bigint UNSIGNED NOT NULL,
  `group_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`friendship_id`,`friend_id`,`friend_type`,`group_id`),
  KEY `friendship_groups_friend_type_friend_id_index` (`friend_type`,`friend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interactions`
--

DROP TABLE IF EXISTS `interactions`;
CREATE TABLE IF NOT EXISTS `interactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `relation` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'follow' COMMENT 'follow/like/subscribe/favorite/upvote/downvote',
  `relation_value` double DEFAULT NULL,
  `relation_type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interactions_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `interactions_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE IF NOT EXISTS `lessons` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `content` text COLLATE utf8mb3_unicode_ci,
  `video_url` text COLLATE utf8mb3_unicode_ci,
  `youtube_url` text COLLATE utf8mb3_unicode_ci,
  `duration` int DEFAULT NULL,
  `min_read` int DEFAULT '1',
  `view_count` int NOT NULL DEFAULT '0',
  `like_count` int DEFAULT '0',
  `dislike_count` int NOT NULL DEFAULT '0',
  `comment_count` int NOT NULL DEFAULT '0',
  `share_count` int NOT NULL DEFAULT '0',
  `download_count` int NOT NULL DEFAULT '0',
  `assigned_groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `status` enum('0','1') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `order` int UNSIGNED DEFAULT NULL,
  `point_tuition_fee` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_course_id_foreign` (`course_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_images`
--

DROP TABLE IF EXISTS `lesson_images`;
CREATE TABLE IF NOT EXISTS `lesson_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_images_lesson_id_foreign` (`lesson_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `liked_posts`
--

DROP TABLE IF EXISTS `liked_posts`;
CREATE TABLE IF NOT EXISTS `liked_posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `liked_posts_user_id_foreign` (`user_id`),
  KEY `liked_posts_post_id_foreign` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `liked_post_comments`
--

DROP TABLE IF EXISTS `liked_post_comments`;
CREATE TABLE IF NOT EXISTS `liked_post_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `liked_post_comments_user_id_foreign` (`user_id`),
  KEY `liked_post_comments_post_comment_id_foreign` (`post_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mental_maths`
--

DROP TABLE IF EXISTS `mental_maths`;
CREATE TABLE IF NOT EXISTS `mental_maths` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint UNSIGNED NOT NULL,
  `receiver_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by_receiver` tinyint(1) NOT NULL DEFAULT '0',
  `attachment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_06_02_231912_create_donate_recipients_table', 1),
(2, '2024_06_02_233248_create_acquaintances_friendship_table', 2),
(3, '2024_06_02_233248_create_acquaintances_friendships_groups_table', 3),
(4, '2024_06_02_233248_create_acquaintances_interactions_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT '0',
  `action_url` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sender_id` bigint UNSIGNED DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb3_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plearnd_admins`
--

DROP TABLE IF EXISTS `plearnd_admins`;
CREATE TABLE IF NOT EXISTS `plearnd_admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plearnd_admins_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `plearnd_admins`
--

INSERT INTO `plearnd_admins` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-06-02 06:45:47', '2024-06-02 06:45:47');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
CREATE TABLE IF NOT EXISTS `polls` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `max_votes` int NOT NULL DEFAULT '1',
  `is_multiple_choice` tinyint(1) NOT NULL DEFAULT '0',
  `total_votes` bigint UNSIGNED NOT NULL DEFAULT '0',
  `image_url` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `polls_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '3',
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `engagement_rate` double NOT NULL DEFAULT '0',
  `post_type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `source_platform` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `interacted_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE IF NOT EXISTS `post_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_post_comment_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hashtags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_post_id_foreign` (`post_id`),
  KEY `post_comments_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `post_comment_images`
--

DROP TABLE IF EXISTS `post_comment_images`;
CREATE TABLE IF NOT EXISTS `post_comment_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint UNSIGNED NOT NULL,
  `post_comment_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comment_images_post_id_foreign` (`post_id`),
  KEY `post_comment_images_post_comment_id_foreign` (`post_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_images`
--

DROP TABLE IF EXISTS `post_images`;
CREATE TABLE IF NOT EXISTS `post_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb3_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_images_post_id_foreign` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_image_comments`
--

DROP TABLE IF EXISTS `post_image_comments`;
CREATE TABLE IF NOT EXISTS `post_image_comments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `replies` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_image_comments_user_id_foreign` (`user_id`),
  KEY `post_image_comments_post_image_id_foreign` (`post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_image_comment_dislikes`
--

DROP TABLE IF EXISTS `post_image_comment_dislikes`;
CREATE TABLE IF NOT EXISTS `post_image_comment_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_image_comment_dislikes_user_id_foreign` (`user_id`),
  KEY `post_image_comment_dislikes_post_image_comment_id_foreign` (`post_image_comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_image_comment_likes`
--

DROP TABLE IF EXISTS `post_image_comment_likes`;
CREATE TABLE IF NOT EXISTS `post_image_comment_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_comment_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_image_comment_likes_user_id_foreign` (`user_id`),
  KEY `post_image_comment_likes_post_image_comment_id_foreign` (`post_image_comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_image_dislikes`
--

DROP TABLE IF EXISTS `post_image_dislikes`;
CREATE TABLE IF NOT EXISTS `post_image_dislikes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_image_dislikes_user_id_foreign` (`user_id`),
  KEY `post_image_dislikes_post_image_id_foreign` (`post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_image_likes`
--

DROP TABLE IF EXISTS `post_image_likes`;
CREATE TABLE IF NOT EXISTS `post_image_likes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `post_image_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_image_likes_user_id_foreign` (`user_id`),
  KEY `post_image_likes_post_image_id_foreign` (`post_image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `questionable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `questionable_id` bigint UNSIGNED NOT NULL,
  `text` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `correct_answers` int DEFAULT NULL,
  `correct_option_id` int UNSIGNED DEFAULT NULL,
  `explanation` text COLLATE utf8mb3_unicode_ci,
  `difficulty_level` enum('easy','medium','hard') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `time_limit` int DEFAULT NULL,
  `points` int NOT NULL,
  `position` int DEFAULT NULL,
  `tags` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_user_id_foreign` (`user_id`),
  KEY `questions_questionable_type_questionable_id_index` (`questionable_type`,`questionable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_images`
--

DROP TABLE IF EXISTS `question_images`;
CREATE TABLE IF NOT EXISTS `question_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `imageable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `imageable_id` bigint UNSIGNED NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_images_imageable_type_imageable_id_index` (`imageable_type`,`imageable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

DROP TABLE IF EXISTS `question_options`;
CREATE TABLE IF NOT EXISTS `question_options` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `optionable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `optionable_id` bigint UNSIGNED NOT NULL,
  `text` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `explanation` text COLLATE utf8mb3_unicode_ci,
  `position` int DEFAULT NULL,
  `status` enum('active','archived','deleted') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_options_optionable_type_optionable_id_index` (`optionable_type`,`optionable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb3_unicode_ci,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('mmGNlzBVonNhtvNk0FBQnhtsWa3idlcIkOLAMyUR', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWEVMa08zTmQ1SE1TSTFKYTVOczdNZERURXhtYXR4QnI5WWpsdExxTiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEyJDZTVTZtRy5HalBXaXo4VWJDRzdVNk9QZjlEMnBMRzBzVHdqT2M4ZkpnanBKbWZ0dHlxYlNtIjt9', 1717464228),
('wogXEfZsOCvQe4ixOaajcpS4RMoLZ4WL9WbvM4y3', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFAwQUJiNjFHMmdOV0RJR1BOSllyWmxkWU43aHBaZ3BPdUZzSFBiMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1717464052),
('cW9TIDL6c8UWqAAJwydHmthyRFMsptqIfW5RkFxw', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiWjBMNG1kekdzWmQ5ZHhGUE9ZZHlXZ2U3bzVQVDJaWHFRbjdxbjExViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbmV3c2ZlZWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MjE6InBhc3N3b3JkX2hhc2hfc2FuY3R1bSI7czo2MDoiJDJ5JDEyJDZTVTZtRy5HalBXaXo4VWJDRzdVNk9QZjlEMnBMRzBzVHdqT2M4ZkpnanBKbWZ0dHlxYlNtIjt9', 1717456739);

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

DROP TABLE IF EXISTS `supports`;
CREATE TABLE IF NOT EXISTS `supports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `supporter` bigint UNSIGNED DEFAULT NULL,
  `amounts` int NOT NULL,
  `duration` int NOT NULL,
  `total_views` int NOT NULL,
  `remaining_views` int NOT NULL,
  `slip` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `media_image` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `media_link` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `transfer_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `transfer_time` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supports_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
CREATE TABLE IF NOT EXISTS `topics` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `academy_id` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  `lesson_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb3_unicode_ci,
  `status` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'published',
  `likes` int NOT NULL DEFAULT '0',
  `dislikes` int NOT NULL DEFAULT '0',
  `comments` int NOT NULL DEFAULT '0',
  `shares` int NOT NULL DEFAULT '0',
  `views` int NOT NULL DEFAULT '0',
  `hashtags` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'public',
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sentiment` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `engagement_rate` double NOT NULL DEFAULT '0',
  `source_platform` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `interacted_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topics_user_id_foreign` (`user_id`),
  KEY `topics_course_id_foreign` (`course_id`),
  KEY `topics_lesson_id_foreign` (`lesson_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `topic_images`
--

DROP TABLE IF EXISTS `topic_images`;
CREATE TABLE IF NOT EXISTS `topic_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic_id` bigint UNSIGNED NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_images_topic_id_foreign` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `twitter_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `linkedin_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `suggester_code` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '99999999',
  `personal_code` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `reference_code` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `no_of_ref` int NOT NULL DEFAULT '0',
  `pp` bigint UNSIGNED NOT NULL DEFAULT '0',
  `wallet` bigint UNSIGNED NOT NULL DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `two_factor_secret` text COLLATE utf8mb3_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb3_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `current_team_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_name_unique` (`name`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_personal_code_unique` (`personal_code`),
  UNIQUE KEY `users_reference_code_unique` (`reference_code`),
  UNIQUE KEY `users_phone_number_unique` (`phone_number`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  UNIQUE KEY `users_facebook_id_unique` (`facebook_id`),
  UNIQUE KEY `users_twitter_id_unique` (`twitter_id`),
  UNIQUE KEY `users_linkedin_id_unique` (`linkedin_id`),
  UNIQUE KEY `users_github_id_unique` (`github_id`)
) ENGINE=MyISAM AUTO_INCREMENT=201 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `google_id`, `facebook_id`, `twitter_id`, `linkedin_id`, `github_id`, `suggester_code`, `personal_code`, `reference_code`, `no_of_ref`, `pp`, `wallet`, `verified`, `email_verified_at`, `phone_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `created_at`, `updated_at`) VALUES
(1, 'Utai Salem', 'utaisalem@gmail.com', '0938403000', NULL, NULL, NULL, NULL, NULL, '55555555', '11111111', 'ac2q9xBynt', 3, 0, 0, 0, '2024-05-27 07:23:29', NULL, '$2y$12$6SU6mG.GjPWiz8UbCG7U6OPf9D2pLG0sTwjOc8fJgjpJmfttyqbSm', NULL, NULL, NULL, NULL, NULL, '2024-03-11 06:58:39', '2024-06-03 23:18:59'),
(2, 'นายอุทัย สาเหล็ม', 'labcomjsm@gmail.com', '0872880070', NULL, NULL, NULL, NULL, NULL, '11111111', '22222222', 'AvlUpZgaz1', 5, 0, 0, 0, '2024-05-28 05:17:55', NULL, '$2y$12$WDFVo8rez1MPaiMqkteOae89oLIK2YoJdS2V7zGPoy3boeGsDYl7O', NULL, NULL, NULL, NULL, NULL, '2024-03-11 07:02:06', '2024-06-02 18:16:43'),
(146, 'Anas Rakdee', 'desoa1221@gmail.com', '0802911236', NULL, NULL, NULL, NULL, NULL, '30630213', '99707158', 'lfxgIwJ448', 0, 0, 0, 0, NULL, NULL, '$2y$12$WF5h4el2lTgznKn/ovREZeUY0lRW0xWb3YQ9YWZ5B9qvLy6Smi2cS', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:55:29', '2024-05-28 04:55:29'),
(147, 'มาดียะห์', 's10521@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '14777975', '18248171', 'hMBCvBlv3D', 0, 0, 0, 0, NULL, NULL, '$2y$12$5sT0VRFx4dfrOWzRM0Mcmu4xM86ufqki2uzFUF0XnC0ZSou/NhqrC', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:55:56', '2024-05-28 04:55:56'),
(7, 'Plearnd', 'aplearnd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '22222222', '33333333', 'PrpvZok6KT', 5, 0, 0, 0, '2024-05-28 04:16:39', NULL, '$2y$12$83QIyNthBcGjSXlC0Et/QuvkaQ2hitR39KIcB06wL3zxUee8prfim', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:15:57', '2024-05-29 03:28:49'),
(145, 'nattasud sudpakdee', 's10507@gmail.com', '0989642158', NULL, NULL, NULL, NULL, NULL, '30630213', '31957688', 'GsCmG0IrBQ', 0, 0, 0, 0, NULL, NULL, '$2y$12$JoQ/quNzm3RW2xaYSuH.POukoyS3XldGQ7HDLhe1Ls5bANl.Xbgsu', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:55:19', '2024-05-28 04:55:19'),
(144, 'รูซีญา เบ็ญสะอิ', 's10520@jariyathum.ac.th', '0807048903', NULL, NULL, NULL, NULL, NULL, '14777975', '16638320', 'opHRVKPwhG', 3, 0, 0, 0, NULL, NULL, '$2y$12$Yix.bPl/jcX8mTVOvUjVBOIHcrkODRVvJOymF7czBWNGVTnua7Naa', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:55:14', '2024-05-28 04:57:25'),
(143, 'ตัสนีม บาเหม', 's9193@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '14777975', '59961405', '06vPj1btkr', 0, 0, 0, 0, NULL, NULL, '$2y$12$qft5mYYlhPNv6eTdlVaCYOkeH8JbWdmKLz6PwkiP1U8FSagUeREP.', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:55:02', '2024-05-28 04:55:02'),
(142, 'สะดีหย๊ะ หมัดหลี', 's10512@jariyathum.ac.th', '0947626854', NULL, NULL, NULL, NULL, NULL, '95462718', '24228024', 'zPKHVunIsW', 0, 0, 0, 0, NULL, NULL, '$2y$12$HX7jBOlUsGLfPOefmbXGte.PFwnhMzy1i1QOrj0Dw12lLvztXxvjG', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:44', '2024-05-28 04:54:44'),
(141, 'จิตรกร เจ๊ะวัง', 's8795@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '29408505', '23801938', 'JePJyACIav', 0, 0, 0, 0, NULL, NULL, '$2y$12$yYNKyaaoxKwyJ5/Q9Qcza.CLh2EI03NLpZlQBrmiSsrHEaX7JCzry', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:17', '2024-05-28 04:54:17'),
(140, 'nabila', 's8918@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '29408505', '69081714', 'l5OfjGLu82', 0, 0, 0, 0, NULL, NULL, '$2y$12$WEtwCsWoxM/ZxGcagpVcweXAyVr/m87rW.kaoR.J3LgeVKU0iqQ2S', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:16', '2024-05-28 04:54:16'),
(139, 'นูรียะฮ์  ล๊ะล่องส๊ะ', 's8768@jariyathum.ac.th', '0932201749', NULL, NULL, NULL, NULL, NULL, '95462718', '11552912', 'ssN8T0S4mX', 0, 0, 0, 0, NULL, NULL, '$2y$12$CJvQbI/wzIWww0E3qBF8juYuoti0t582LXOSuNAV8oi2pBnx1oPiy', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:12', '2024-05-28 04:54:12'),
(138, 'zuhaime ratchamrong', 's8892@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '29408505', '72773951', 'gYO2LA0rwF', 0, 0, 0, 0, NULL, NULL, '$2y$12$.em60r4JXDvYcxMtdyU0Y.9yxqyjRfLiHUkMqc73C1k8.zaZDvvTa', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:07', '2024-05-28 04:54:07'),
(137, 'มีรนา หะยีอับดุลลอฮ', 'mirnahajiabdullah@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '29408505', '59529059', 'RBiIs53Vgs', 0, 0, 0, 0, NULL, NULL, '$2y$12$amkF7SFC3tim1foh.EQ.ru3bYeOZ9UX3w4zpNmzNYbZbGF9d8iunu', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:54:05', '2024-05-28 04:54:05'),
(136, 'อัลมิตรา เจ๊ะหลง', 's7019@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '29408505', '14777975', 'gGghHbwPdL', 3, 0, 0, 0, NULL, NULL, '$2y$12$NEG9BLPCRxMYS8UJ53AlHuQH/4hSsWP6Xm5/YOvOShy0TbdRGy2dm', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:50', '2024-05-28 04:55:56'),
(135, 'เซาด๊ะฮ์ ชายเหร็น', 's8944@jariyathum.ac.th', '0644802370', NULL, NULL, NULL, NULL, NULL, '54779272', '95462718', 'NnZiOyc8PU', 3, 0, 0, 0, NULL, NULL, '$2y$12$Cx5pZiiZ39E9FkicgiEgeuO63WIrqyQLHc2FVbsVoPtmlbJ1QCZdW', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:19', '2024-05-28 04:57:42'),
(134, 'เอกวุฒิ หมานระโต๊ะ', 's9177@jarythum.ac.th', '0812659055', NULL, NULL, NULL, NULL, NULL, '54779272', '75286198', 'G1k0CrnrYi', 1, 0, 0, 0, NULL, NULL, '$2y$12$tugydTiqVS9TMjFD1GXMMebu0zA4gkcHM2Eli97Wq614Ot7NPBIVK', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:18', '2024-05-28 04:58:01'),
(133, 'มูฮัมหมัดอาลี เหล็มโส๊ะ', 's8843@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '54779272', '51311626', 'dhG3N6iiEV', 3, 0, 0, 0, NULL, NULL, '$2y$12$rVcZYpXEo1SI6JpQHSgMM.4MX2/qz.LnccyW/Z/fqML.jRaCBFx0S', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:13', '2024-05-28 04:58:12'),
(132, 'วานิตา ยีกับจี', 's8880@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '54779272', '29408505', 'ACdRfYBo8H', 5, 0, 0, 0, NULL, NULL, '$2y$12$Z/wY7cWV1t5l7YmAeFkqc.5te4csseAJDyZvDVTb96/rxX7JO8cGu', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:10', '2024-05-28 04:54:17'),
(131, 'Aekkapong Loh-oh', 's8800@jariyathum.ac.th', '0855060046', NULL, NULL, NULL, NULL, NULL, '54779272', '30630213', 'xamPDV3KHX', 4, 0, 0, 0, NULL, NULL, '$2y$12$SU0PnqduRmEYdowDXwmbyOq6MLsaBu04OFHHrw24D3QnsarT4E0Ji', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:53:08', '2024-05-28 04:56:05'),
(130, 'อนุรักษ์ มะอะหมีน', 's8885@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '22222222', '58502565', 'k3U2lkeQPX', 0, 0, 0, 0, NULL, NULL, '$2y$12$jQ655lfU84EW6FSs2/A/xO0w6vStXhpOV.MpFxVFo1hxEGI9nwVAO', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:51:58', '2024-05-28 04:51:58'),
(129, 'ชนะพล ไขยแก้ว', 's8853@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '22222222', '54779272', '4bnQQtQ4j6', 5, 0, 0, 0, NULL, NULL, '$2y$12$azv3IdSvQ2dkuTtMZP4lzOmyuZ/rxYRJWkwvZtvNQacnqiwE58R1a', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:51:53', '2024-05-28 04:53:19'),
(148, 'กษิมา บิลโส๊ะ', 's10518@jariyathum.ac.th', '0979548685', NULL, NULL, NULL, NULL, NULL, '51311626', '49144709', '9LUYCK3zNO', 1, 0, 0, 0, NULL, NULL, '$2y$12$6491ekjL5sh0GDVLcyNRN.7DkWPqLC6/uDTZWjSgTBVwa2vk.dVXi', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:56:04', '2024-05-28 04:57:29'),
(149, 'อดีฟ แตกอย', 'askydaz@gmail.com', '0611753543', NULL, NULL, NULL, NULL, NULL, '30630213', '97700813', 't9AxvUO69w', 0, 0, 0, 0, NULL, NULL, '$2y$12$Ej4rz1xXVCTzxAj3ypeqzuCcikELDhrzFBw9f8OghN4VhYmaczhce', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:56:04', '2024-05-28 04:56:04'),
(150, 'Muhammadbassa toharewea', 's8835@jariyathum.ac.th', '0822243872', NULL, NULL, NULL, NULL, NULL, '30630213', '32724970', 'vrzmkHXg2R', 0, 0, 0, 0, NULL, NULL, '$2y$12$MqwFwA06Y92NMfMr7QQOX.o0p0B.ESXzp/yw.f2MqbEAFJgU0ycrK', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:56:05', '2024-05-28 04:56:05'),
(151, 'อุษณีย์ โต๊ะปง', 'sunee3457@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '16638320', '31783455', 'RRynLDWsGA', 0, 0, 0, 0, NULL, NULL, '$2y$12$w92Mji88xA9Hb3uCJmMvsezTDO3dOyZKgAKE/2UbZ.A2kJgrwwfbW', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:56:13', '2024-05-28 04:56:13'),
(152, 'อนัญดา วรรณกิจ', 's9079@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '16638320', '53258817', '8bkEKjh4QV', 0, 0, 0, 0, NULL, NULL, '$2y$12$GTaX7jJ89EpWnsgmoJIh5OfappnvyPH9eBSOgNiVMoTqSEvpFNK2e', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:57:15', '2024-05-28 04:57:15'),
(153, 'nonlanee', 'nonlaneetnesamorpong@gmail.com', '0949488427', NULL, NULL, NULL, NULL, NULL, '51311626', '84769764', 'yqnB1Svivt', 0, 0, 0, 0, NULL, NULL, '$2y$12$N459tY43SXF3I/qIH0rHJOtBdSm0.FDgKa/lzE22HOTptLMW42EJC', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:57:18', '2024-05-28 04:57:18'),
(154, 'นาซีเราะฮ์ ราชชำรอง', 's8747@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '16638320', '35828470', 'znBK26Pb7p', 0, 0, 0, 0, NULL, NULL, '$2y$12$.v.PkWxCUQFhHSQx9RSy0uKnI5h1yNkqbdvyGhAmVPyKQbfavAQKm', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:57:25', '2024-05-28 04:57:25'),
(155, 'ซานีต้า หวันชิดนาย', 's10511@jariyathum.ac.th', '0936041685', NULL, NULL, NULL, NULL, NULL, '49144709', '10431491', 'PxlrqzCYIg', 0, 0, 0, 0, NULL, NULL, '$2y$12$3K21P60GoTr84J1a/P3QSuCJYsJPt.ysQ0YRn7ODZSJNen.qF2Hu6', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:57:29', '2024-05-28 04:57:29'),
(156, 'ณัฐกานต์ มะหลีโดง', 's9788@jariyathum.ac.th', '0640585777', NULL, NULL, NULL, NULL, NULL, '95462718', '78822108', 'o6sQomN8wN', 0, 0, 0, 0, NULL, NULL, '$2y$12$OVUVN1QylWJiU7Be.FokUuwA5FBUuXSlV19gdHzpTIonX/RAAkc1q', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:57:42', '2024-05-28 04:57:42'),
(157, 'panawat leatee', 's10508@jariyatham.ac.th', '0633240184', NULL, NULL, NULL, NULL, NULL, '75286198', '58390611', 'JlLvUPibsM', 0, 0, 0, 0, NULL, NULL, '$2y$12$rvkYF16NxwmP4o/ZlCyHJu0RmsdcZijvXO2mnFOaE9qVJQ7/MfJjO', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:58:01', '2024-05-28 04:58:01'),
(158, 'sanma nuisaman', 's9084@jariyathum.ac.th', '0641368072', NULL, NULL, NULL, NULL, NULL, '51311626', '91727848', 'gWsQMUKiVi', 0, 0, 0, 0, NULL, NULL, '$2y$12$bru9VFT9b3YAvjiHHB9Bs.JNfuIUH0OeesEIAb09zIt7J4B.74LF.', NULL, NULL, NULL, NULL, NULL, '2024-05-28 04:58:12', '2024-05-28 04:58:12'),
(9, 'Utai 0933xxx', 'utai0938403000@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '11111111', '55555555', 'Kv5pKBYd6p', 5, 0, 0, 0, '2024-05-28 08:02:12', NULL, '$2y$12$/JItCbrzvMXJsSywN3vHj.wvo.UVlvEBxaWfBQiEyLw1t48w23rWi', NULL, NULL, NULL, NULL, NULL, '2024-05-28 06:47:09', '2024-05-29 04:34:49'),
(160, 'อาฟันดี อิเยาะ', 'afandee254831@gmail.com', '0985683060', NULL, NULL, NULL, NULL, NULL, '33333333', '60319416', 'Plh3VxsfPe', 0, 0, 0, 0, NULL, NULL, '$2y$12$AVFFW7tUiExYQGMr2t10Z.A4ROY.9c3sTZcjYEHpmMz4I2Fy/8Aby', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:27:08', '2024-05-29 03:27:08'),
(161, 'Anucha Buyusoh', 'anuchajsm@gmail.com', '0992061325', NULL, NULL, NULL, NULL, NULL, '33333333', '98601785', 'SyAlC10Wza', 1, 0, 0, 0, '2024-05-29 03:32:10', NULL, '$2y$12$Os5UQWcviBfmIDQQbW9bmOoS0MIjqL3uvh72VZaG5.t48.bIO6PIK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:27:40', '2024-05-29 03:37:08'),
(162, 'ฮานีฟ เพ็ชยาวา', 'haneefharis2559@gmail.com', '0917369272', NULL, NULL, NULL, NULL, NULL, '33333333', '50195372', 'dkAuaM5xj7', 1, 0, 0, 0, '2024-05-29 03:29:12', NULL, '$2y$12$V8Y.OhhXCoINxq5AJsrG7.8gfWRR0pEyrYcM.H3m9f8BVDwLWVnD6', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:28:22', '2024-05-29 03:35:39'),
(163, 'ซาบามาเรียม อุดดิน', 'sabamariam10810@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '33333333', '46348116', 'Pl7ah2r50Q', 0, 0, 0, 0, '2024-05-29 03:31:37', NULL, '$2y$12$1dPmyRHaTKtlzGHiT1nWUOu9alFlN2cnm1eZqWoEIfRArrUUHRXl.', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:28:33', '2024-05-29 03:31:37'),
(164, 'สมปรารถนา หวันมะแส', 's8909@jariyathum.ac.th', '0810784464', NULL, NULL, NULL, NULL, NULL, '33333333', '67647483', 'XzZ4DEqS0T', 1, 0, 0, 0, NULL, NULL, '$2y$12$UTlperzWV8jSlm73CzdqJO/oujmyVYyNHMWqfEifCZxQyK8wvTiCi', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:28:49', '2024-05-29 03:32:41'),
(165, 'อาซีซ๊ะ  ตาเยะ', '8924cutegxmck4@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '67647483', '38169848', '8BElAc8ocK', 0, 0, 0, 0, '2024-05-29 03:36:17', NULL, '$2y$12$LSbnhDE2IR9KFA9hrSZhjOKakNq4GgTdmuIJvCtDVtspD1NiNeZsS', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:32:41', '2024-05-29 03:36:17'),
(166, 'มูฮัมหมัดบรัยอัล เด็นหลี', 's8900@jariyathum.ac.th', '0633498029', NULL, NULL, NULL, NULL, NULL, '50195372', '79004943', 'HJS3OZtwew', 0, 0, 0, 0, NULL, NULL, '$2y$12$BbEnL3nDYY1KgAMHaDaUku6UwtmRg9xMqhQxiSP1oJK/6iZqJHs.G', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:35:39', '2024-05-29 03:35:39'),
(167, 'farif mujahteh', 's10545@jariyathum.ac.th', '0980189433', NULL, NULL, NULL, NULL, NULL, '98601785', '30610141', 'dMXKKNo1ia', 0, 0, 0, 0, NULL, NULL, '$2y$12$5SfhOkJkzCDTz41kRDl2ruZD3JqFbKffmGi9n0kqLloZ6UI1Tmege', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:37:08', '2024-05-29 03:37:08'),
(8, 'Phupha LabcomJSM', 'labcomjsm@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '33333333', '44444444', 'cZMqIVcp7U', 5, 0, 0, 0, '2024-06-01 09:18:09', NULL, '$2y$12$sO3Ogzt0MdaPKAvW9cZhqeX6KJH2dBnkUachxJErlAQIXJciSvTgC', NULL, NULL, NULL, NULL, NULL, '2024-05-29 03:54:43', '2024-06-01 09:18:09'),
(169, 'อัศนัย แกนุ้ย', 's5967@jariyathum.ac.th', '0937429273', NULL, NULL, NULL, NULL, NULL, '44444444', '24753642', '7C2B53k3td', 0, 0, 0, 0, NULL, NULL, '$2y$12$4k2zoS3pKltZUVyU6O7Xce6VuCEMrjjZ5jUwsL/Zm4zdhL3WwZm92', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:14:12', '2024-05-29 04:14:12'),
(170, 'ketsaralaehsan', 's10045@jariyathum.ac.th', '0627435930', NULL, NULL, NULL, NULL, NULL, '44444444', '11281413', 'KnS30prAqV', 0, 0, 0, 0, NULL, NULL, '$2y$12$YL8tYvlbt7zudeXsxcSo1ubQxHitWE6p5y82QqMRT3Htl0mL6wa6u', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:15:31', '2024-05-29 04:15:31'),
(171, 'nattawut srirat', 's5503@jarithum.ac.th', '0623315219', NULL, NULL, NULL, NULL, NULL, '44444444', '29400035', '7J3Wr2POy8', 0, 0, 0, 0, NULL, NULL, '$2y$12$eCMzKtARV7ZLRMYSjXopMOIDT3fkelLeU/d1gY.yXwboyg/EbQebu', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:15:31', '2024-05-29 04:15:31'),
(172, 'ธวัชชัย หว่าหลำ', 's9969@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '44444444', '26437035', 'vtgDMCsras', 0, 0, 0, 0, NULL, NULL, '$2y$12$cUes6iN4hjPen5wSQV8m5upYdHp1rSnkHxtVrgHMcOx/PWk4.IRH2', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:15:43', '2024-05-29 04:15:43'),
(173, 'ธนวัตร สาระภี', 's10620@JARIYATHUM.ac.th', '0937271082', NULL, NULL, NULL, NULL, NULL, '44444444', '63952230', 'FEVnUhEnDp', 0, 0, 0, 0, NULL, NULL, '$2y$12$ThsaJbutkiuYhA1OHsunAOub5ONuDxuqYweIRD8XjeD8ZXyR0FsQm', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:16:00', '2024-05-29 04:16:00'),
(174, 'ชากีร', 's9890@jarythum.ac.th', '0936646774', NULL, NULL, NULL, NULL, NULL, '55555555', '94493719', 'ox7FWuEI1L', 3, 0, 0, 0, NULL, NULL, '$2y$12$N8x3t.eknVz4OR2AHrpnLOl5Wmuuv9AFWbuFpCSaC/U8KkxAFDxCO', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:34:15', '2024-05-29 04:47:55'),
(175, 'บัดรีย์ มัจฉาวานิช ม2/3', 's9880@jariyathum.ac.th', '0872877121', NULL, NULL, NULL, NULL, NULL, '55555555', '95718715', '3GjtADtM2K', 1, 0, 0, 0, NULL, NULL, '$2y$12$hc.BvSE11ZCS.ETQYvOrDeo.qQGbIfe6aIBCTSKV9ebcyCE8UXf1q', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:34:20', '2024-05-29 04:38:15'),
(176, 'ฟิรฮาน เหมหมาด', 's10236@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '55555555', '72123355', 'FdzzXN8bTu', 1, 0, 0, 0, NULL, NULL, '$2y$12$VJwnZHbH00EV18s8Ag/Ax.iXpflx.2F5RVhgOkMKnVMLGVsZyHLRa', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:34:24', '2024-05-29 04:36:49'),
(177, 'ไซนุดดดีน โอะโร', 's9873@jariyathum.ac.th', '0941287363', NULL, NULL, NULL, NULL, NULL, '55555555', '39536411', 'hwz2y33Bms', 2, 0, 0, 0, NULL, NULL, '$2y$12$PjwQNY3IzuHfZvRIaMcyj.jmXuSE16cESkybO/fd/ZBgliZ7Jsika', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:34:37', '2024-05-29 04:39:07'),
(178, 'มูญาฮีด มะหะหมัดวงษ์', 's9887@jariyathum.ac.th', '0810491947', NULL, NULL, NULL, NULL, NULL, '55555555', '27956764', 'vAmtLnKs5F', 4, 0, 0, 0, NULL, NULL, '$2y$12$vibmWyiRzQJy//felx04ROU0zZrDRif4KY4piaKjMSdlDS6AaUHye', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:34:49', '2024-05-29 04:38:22'),
(179, 'มูฮัมหมัดบัชชาร ยุติกา', 's9883@jariyatham.ac.ch', NULL, NULL, NULL, NULL, NULL, NULL, '72123355', '13634157', 'PmmpnOY1Kb', 1, 0, 0, 0, NULL, NULL, '$2y$12$8x7qzeKgv1t8t10ruNSD0Oh5lXmPlJa8bs.lT4IfMNgGGSrh62ixq', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:36:49', '2024-05-29 04:42:47'),
(180, 'วรภพ ภิญโญ', 's9881@jariyathum.ac.th', '0824825146', NULL, NULL, NULL, NULL, NULL, '27956764', '84595887', '4TxM18zyGG', 0, 0, 0, 0, NULL, NULL, '$2y$12$N.WyjgjUYZvWlZr44LgeXu.hGw2iBg3u7SRp0oAklNZFPUlXZ/6Uq', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:37:29', '2024-05-29 04:37:29'),
(181, 'อัชรอฟ บินล่าเต๊ะ', 's9866@jariyathum.ac.th', '0983302051', NULL, NULL, NULL, NULL, NULL, '27956764', '62667125', 'qWCPvBe6vl', 3, 0, 0, 0, NULL, NULL, '$2y$12$QR2QgVSxsywHVDP2oHEnv.mPencTPOmUoGbw4z5qgVPzfl3ioRqti', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:00', '2024-05-29 04:43:00'),
(182, 'วรพงษ์ คำทอง', 's10615@jariyatham.ac.th', '0990824941', NULL, NULL, NULL, NULL, NULL, '94493719', '68846592', 'WHZLgsOwrx', 0, 0, 0, 0, NULL, NULL, '$2y$12$doCqIn8upqJ37/yZlI/HVOMduYD8eMe/wf.N7o3AJIB1yFzNxPhUC', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:04', '2024-05-29 04:38:04'),
(183, 'บุรฮาน  อ่าวลึกน้อย', 's9885@jariyathum.ac.th', '0993213716', NULL, NULL, NULL, NULL, NULL, '27956764', '34395492', 'FZG17Z7dTw', 0, 0, 0, 0, NULL, NULL, '$2y$12$5d/e0qYj5.DSshdING0SZOGIqu56GOm89VIv.QNw/PnJWQ5Z6lzdq', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:08', '2024-05-29 04:38:08'),
(184, 'ชามิล หมานเด', 's9874@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '95718715', '30331743', 'I8hvz7l3zV', 0, 0, 0, 0, NULL, NULL, '$2y$12$3vmLe313SAp8xucxE04KjuoKp8Gmnj/WuAtZC8GJyNnGuTZPHcM3S', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:15', '2024-05-29 04:38:15'),
(185, 'Aniwut Janjue', 's9875@jariyathum.ac.th', '0982980133', NULL, NULL, NULL, NULL, NULL, '39536411', '42973679', 'wXk4qIzMPO', 0, 0, 0, 0, NULL, NULL, '$2y$12$eFGd/dStX97KbGMgnSkOIOEp503OIO/VCrshLmttp7Ir6ed0nEuP2', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:16', '2024-05-29 04:38:16'),
(186, 'รุสกีกานา', 's9879@jariyathum.ac.th', '0994844211', NULL, NULL, NULL, NULL, NULL, '27956764', '59312422', '4R5TVpjatG', 1, 0, 0, 0, NULL, NULL, '$2y$12$FRZwvAnLk9ZxrvurSlzYeO2MceweMPnA8NuqratZNneWBlsncal0a', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:38:22', '2024-05-29 04:40:11'),
(187, 'Asmee Lahtam', 's9959@jariyathum.mc.th', NULL, NULL, NULL, NULL, NULL, NULL, '39536411', '26353639', 'nYNgt9t7Zz', 1, 0, 0, 0, NULL, NULL, '$2y$12$Zd.yJ.8Vn5ydbvEkQX4aJO7lzcU6qy5mFnXuyuLfCxZhJ4noixrGO', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:39:07', '2024-05-29 04:41:20'),
(3, 'Proud BhuPha', 'proudbhupha@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '11111111', '66666666', '7LdfJBUshN', 1, 0, 0, 0, '2024-06-01 10:44:09', NULL, '$2y$12$7u84Z6x8tM70SElXWCTBmOBBzLy1ESNRTyQuZE0zyFtdimla.D/E6', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:40:04', '2024-06-01 10:44:09'),
(189, 'ชาบิล ผุดผ่อง', 's9867@jariyathum.ac.th', '0624866082', NULL, NULL, NULL, NULL, NULL, '59312422', '27760463', 'ahExBb2Kz9', 0, 0, 0, 0, NULL, NULL, '$2y$12$Z.5RXjMVnyFtjL2EqL95jenmw85QY4Rzx5eLVpYpyLAx4/rrVq08a', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:40:11', '2024-05-29 04:40:11'),
(190, 'อาริฟ มาเส็ม', 's10614@jariyathum.ac.th', '0910078096', NULL, NULL, NULL, NULL, NULL, '94493719', '57812125', 'CuZsvEMtbG', 0, 0, 0, 0, NULL, NULL, '$2y$12$5/1.huDpd7mgoaAzi5DHUumTfiOTOmynQk0TyfecY7NdFIxSXOgK2', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:40:35', '2024-05-29 04:40:35'),
(191, 'ยาบิร หลงมีหนา', 'yabir1498@gmail.com', '0948262321', NULL, NULL, NULL, NULL, NULL, '26353639', '90336595', 'hQHKyOXCyM', 1, 0, 0, 0, NULL, NULL, '$2y$12$7JlKhPQiv496raxoXaWJZOwR/lkFX/OFFO.2DT5HtGq7YXOju6gdK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:41:20', '2024-05-29 04:43:01'),
(192, 'zubair', 's9865@jariyathum.ac.th', '0994797759', NULL, NULL, NULL, NULL, NULL, '62667125', '51967700', '7Q3UxSerE6', 0, 0, 0, 0, NULL, NULL, '$2y$12$sILnkGWh1blNccZQpIVaK.tpPBL5R4UvmNDnDRjryRYUTwQceznJy', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:41:35', '2024-05-29 04:41:35'),
(193, 'siraphat', 's9877@jariyathum.ac.th', '0622039304', NULL, NULL, NULL, NULL, NULL, '62667125', '93283136', 'O7d7vapH3Y', 0, 0, 0, 0, NULL, NULL, '$2y$12$jlAC8SbN3NqFRD6HzN4TJ.AoYnUnEgk.r3eiCgrWhcvb8GM7iMgDm', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:42:32', '2024-05-29 04:42:32'),
(194, 'บรรยา สุระกำเเหง', 's9884@jariyatham.ac.ch', NULL, NULL, NULL, NULL, NULL, NULL, '13634157', '92526266', 'J8yqadgGhb', 0, 0, 0, 0, NULL, NULL, '$2y$12$KSk9ksQq9pXc.uccmNcq0uaoJ4MphyyCELaYuMNJ0YRlQUkJR249.', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:42:47', '2024-05-29 04:42:47'),
(195, 'zaidee', 's9872@jariyatum.ac.th', '0842523738', NULL, NULL, NULL, NULL, NULL, '62667125', '54859550', '1CkIghJ0QP', 0, 0, 0, 0, NULL, NULL, '$2y$12$P1wSLS2qzOSxzvx6RJxBluYeWbJyStlhNSROjtSF6N4dLNq31TLBq', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:43:00', '2024-05-29 04:43:00'),
(196, 'อดิสรณ์ อินทร์ด้วง', 'adisorn87062@gmail.com', '098087062', NULL, NULL, NULL, NULL, NULL, '90336595', '98472617', 'lL1nhz1Dxq', 0, 0, 0, 0, NULL, NULL, '$2y$12$CZ/CxdoLGSQ4XHYxGGeyne/oAAJ4q6BBhchTg59jc1FWYD58h.pDW', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:43:01', '2024-05-29 04:43:01'),
(197, 'อัมมาน ดำฤทธิ์', 's9869@jariyathum.ac.th', '0612397179', NULL, NULL, NULL, NULL, NULL, '94493719', '86328445', 'wirqX6uK5n', 0, 0, 0, 0, NULL, NULL, '$2y$12$56tWnQBLhwo1hRfiFLD6oeW1CVu0R97D5uNZGZXMV.L2lHcCRhNMK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 04:47:55', '2024-05-29 04:47:55'),
(198, 'อภิเชษฐ์ มะประสิทธิ์', 's10310@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '11111111', '57654251', 'NMHodss4Vc', 0, 0, 0, 0, NULL, NULL, '$2y$12$9nWUQKFi68hO7denIFIdgO5mTNV9gCHPXSs/szvfmGQYoFcii5sly', NULL, NULL, NULL, NULL, NULL, '2024-05-29 07:42:03', '2024-05-29 07:42:03'),
(199, 'วาสิตา วงศ์อุ่นใจ', 's10002@jariyathum.ac.th', '0612527879', NULL, NULL, NULL, NULL, NULL, '66666666', '41781137', 'X3LfLZW1Go', 5, 0, 0, 0, NULL, NULL, '$2y$12$OzGyE5t5Q8ZDDnl9WMAHbeZijDQdJQGDgPT1ABCp47Zto2K1QEzMa', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:13:03', '2024-05-29 08:17:55'),
(200, 'ญามูนา หวังเหล็ม', 's8469@jariyathum.ac.th', '0812798468', NULL, NULL, NULL, NULL, NULL, '41781137', '96869817', 'hqsa6AXvtc', 2, 0, 0, 0, NULL, NULL, '$2y$12$wOjR3qV5C5M1oTolQLpEAeqSUUg0DHRKobZnw9HS2/HSJfnCkOEx6', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:14:51', '2024-05-29 08:27:34'),
(101, 'อัสมา อุเซ็ง', 's5980@ariyathum.ac.th', '0994487948', NULL, NULL, NULL, NULL, NULL, '41781137', '65695160', 'M3YvZDW0R2', 1, 0, 0, 0, NULL, NULL, '$2y$12$moa7kptdP7oQSgnTbGkxF.whTOGqYYUqII799X1dSswYOBRZNZ2p2', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:14:56', '2024-05-29 08:22:47'),
(102, 'อัสมา อุทัยะาร', 's10035@jariyathum.ac.th', '0642454901', NULL, NULL, NULL, NULL, NULL, '41781137', '34151343', 'N8uaudF2lz', 5, 0, 0, 0, NULL, NULL, '$2y$12$Ftthp5MhOS14kMFPj9.0U.pJBNjKeHRZPENGAORUc93rUaugx48vG', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:16:25', '2024-05-29 08:27:22'),
(103, 'ณัฐธิดา สาหัส', 's9995@jariyathum.ac.th', '0894663204', NULL, NULL, NULL, NULL, NULL, '41781137', '42781456', '4lWxcrI85P', 1, 0, 0, 0, NULL, NULL, '$2y$12$MRLKRVtqFPlsQeCDIZtkfO9pnxIuY3X9cGc0BhR6Uk2h5nSA..fzS', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:16:37', '2024-05-29 08:21:03'),
(104, 'กรวรรณ หลีเจริญ', 'S9990@JARIYATHUM.AC.TH', '0827186247', NULL, NULL, NULL, NULL, NULL, '41781137', '28516500', 'qy6QVYY6tU', 2, 0, 0, 0, NULL, NULL, '$2y$12$haOAlMawxSEdJGTLpP6r2uwljwEfMsg1wwAvZEEWLbffQUM/kgLAe', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:17:55', '2024-05-29 08:25:53'),
(105, 'อศิมา  มูยูโสะ', 's1005@jariyathum.ac.th', '0959920018', NULL, NULL, NULL, NULL, NULL, '28516500', '98554668', 'vI0DLqEDaS', 0, 0, 0, 0, NULL, NULL, '$2y$12$nKvNhRPx2jom06bjbnYM.ON9.I8zP///DohyK0rUxpmX00TiC3yFK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:21:01', '2024-05-29 08:21:01'),
(106, 'อัฟฟาดาร์ เขตเทพา', 's6439@jariyathum.ac.th', '0985650930', NULL, NULL, NULL, NULL, NULL, '42781456', '52865686', 'AwYvTCGp7I', 0, 0, 0, 0, NULL, NULL, '$2y$12$cFJy/HRc5CUkkdBixxn0F.3arCQKilsSvAdDEjeHTxwOgv3yQDiSK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:21:03', '2024-05-29 08:21:03'),
(107, 'นิสริน หมุดลิหมีน', 's9938@jariyathum.ac.th', '0648469595', NULL, NULL, NULL, NULL, NULL, '65695160', '31967603', 'cTGZPVENoN', 0, 0, 0, 0, NULL, NULL, '$2y$12$V1rEES36xM6Nrrum3quLVOnGU/FAe64iJXT8t0kkknspCWm3/wbBa', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:22:47', '2024-05-29 08:22:47'),
(108, 'อานัฐยา เส็มยะมา', 's10008@jariyathum.ac.th', '0698284393', NULL, NULL, NULL, NULL, NULL, '34151343', '62393302', 'QSZ3cvpj0q', 0, 0, 0, 0, NULL, NULL, '$2y$12$s80YxiKwAZ1vWNgwx8EoDO9EiGk6iWIvOUxjYLb4vYAH9rfnf66jG', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:23:43', '2024-05-29 08:23:43'),
(109, 'ดีญานา การดี', 's10003@jariyathum.ac.th', '0625217863', NULL, NULL, NULL, NULL, NULL, '34151343', '24622125', 'oQaStjAsDw', 0, 0, 0, 0, NULL, NULL, '$2y$12$rbpu6k5jzdySRRcZImGGbeVtcGB6z78qIaWfEFh4K6pYmyuwaEZwi', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:24:32', '2024-05-29 08:24:32'),
(110, 'ฮาซานะ มะยิ', 's10037@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '34151343', '35568105', 'MyWz72p0hj', 0, 0, 0, 0, NULL, NULL, '$2y$12$5tcOj/GxHmHy9tK0j2mAeOx/Ypvnui3trV2zNFnzxpqK48e31/oMO', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:25:03', '2024-05-29 08:25:03'),
(111, 'อชิรญา มาเด็น', 's10034@jariyathum.ac.th', '0931424494', NULL, NULL, NULL, NULL, NULL, '96869817', '24499571', 'yturrXQwaB', 0, 0, 0, 0, NULL, NULL, '$2y$12$lHZZfuWBxhl.m9qi/eDTXe7y6HMzxyesht1io9G/Ljb/gesatJeIK', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:25:08', '2024-05-29 08:25:08'),
(112, 'ซารีนาประเสริฐ', 'S5521@JARIYATHUM.AC.TH', '0623616285', NULL, NULL, NULL, NULL, NULL, '34151343', '60190810', 'mqj44E2DW2', 0, 0, 0, 0, NULL, NULL, '$2y$12$OZqdNLNV.n2qFkpuUGbdTuK5a.3Rs/iCxwOjqFWgXLerkZ6ZWlx4G', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:25:33', '2024-05-29 08:25:33'),
(113, 'ซาริลดา หว่าหลำ', 's10004@jariyathum', '0856403261', NULL, NULL, NULL, NULL, NULL, '28516500', '52265161', 'C9gy50A36x', 2, 0, 0, 0, NULL, NULL, '$2y$12$Ogi08qk66.BnLEAj6LEckOAY4RJulSP2DATcfTLo9oDA.cU1plWkW', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:25:53', '2024-05-29 08:30:36'),
(114, 'รสรินทร์ แซ่เก่า', 's10044@jariyathum.ac.th', '0959950820', NULL, NULL, NULL, NULL, NULL, '34151343', '77205111', 'u7Krg67PUF', 0, 0, 0, 0, NULL, NULL, '$2y$12$atKiLuPrVL01t8BJSNqT6.qCKfWEYL7vSsUjUWWljPvcYwS7my0Oi', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:27:22', '2024-05-29 08:27:22'),
(115, 'รุสนีย์ หมีนปาน', 's10001@jariyathum.ac.th', '0825708769', NULL, NULL, NULL, NULL, NULL, '96869817', '31557192', 'Wadcu7tv5y', 0, 0, 0, 0, NULL, NULL, '$2y$12$I.DnoFr7xZaOcUCxYVPjZuj2g3Ldt4UUsuV7KfIoycu/NykZLmfty', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:27:34', '2024-05-29 08:27:34'),
(116, 'สุคนธวา ขวัญเพ็ชร์', 's10014@jariyatum', '0633696065', NULL, NULL, NULL, NULL, NULL, '52265161', '38374050', 'NfgFsBDywv', 0, 0, 0, 0, NULL, NULL, '$2y$12$TZAZB9dTmD4tpsNmnpo0FObzKwgDl0rv2lk7LYFqOPAXqwvHk88Cq', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:28:34', '2024-05-29 08:28:34'),
(117, 'ิิิิิิิิิิฺฺฺิิฺฺฺฺฺฺฺฺิิิิฺฺิฺฺฺิฺฮุสนา  หมีนโส๊ะ', 'S568@jariyathum', '0990783295', NULL, NULL, NULL, NULL, NULL, '52265161', '23868039', 'EsPG5DuGli', 1, 0, 0, 0, NULL, NULL, '$2y$12$x32YeFogUwD24sVl4537feV.EHTvTUY1ZMqjepPXjILbZSlVPhQk2', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:30:36', '2024-05-29 08:52:25'),
(118, 'ซุลกิฟลี จิมัน', 's10050@jariyathum.ac.th', '0636156220', NULL, NULL, NULL, NULL, NULL, '23868039', '19802027', '9CC3r48mlp', 5, 0, 0, 0, NULL, NULL, '$2y$12$GIMGZ9I5ZJNmUzikimJdluNDxafNNZGx4ogapxVEt/FvXXRLlg7he', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:52:25', '2024-05-29 08:55:39'),
(119, 'Tnanakorn_Buhamad', 's10072@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '19802027', '18486893', 'BXkd36R7AX', 1, 0, 0, 0, NULL, NULL, '$2y$12$s2anMlDf/SwQpigJB/3YwOn6ehsZV1jbmJ90loub7odZTYsKnDbWu', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:54:25', '2024-05-29 08:58:09'),
(120, 'ด.ช ฟะฮ์มี บาหะหมะ', 's10066@jariyathum.ac.th', '0655828140', NULL, NULL, NULL, NULL, NULL, '19802027', '79781164', 'Ssd6C4ZKfh', 0, 0, 0, 0, NULL, NULL, '$2y$12$/ovsTRtpXm1bTML4GbfIKuG7UUfOmrSkkTtydfQbY0EPNX4KxfGDe', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:54:55', '2024-05-29 08:54:55'),
(121, 'นิติวัฒน์  เต๊ะเหย๊าะ', 's10061@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '19802027', '23428890', 'I6xOwNPvoI', 0, 0, 0, 0, NULL, NULL, '$2y$12$NkXHggT2xsHUsPZFxiVbveZZIpx6iuO04dVPrmm.HINNvDJM9NWiC', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:55:06', '2024-05-29 08:55:06'),
(122, 'สุรเชษฐ์ หวังเเละ', 's10053@jariyathum.ac.th', '0937391376', NULL, NULL, NULL, NULL, NULL, '19802027', '50824863', 'v8DXyZTbts', 4, 0, 0, 0, NULL, NULL, '$2y$12$upWT9EaVdk/.OCXwKZsrhOIukMsp7Rjzytk5XS8HrETUaquio4X.C', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:55:34', '2024-05-29 09:09:46'),
(123, 'มะอาดีลัน กาเรง', 's10130@jariyathum.ac.th', '0659024434', NULL, NULL, NULL, NULL, NULL, '19802027', '25056002', 'COCSrNJctf', 0, 0, 0, 0, NULL, NULL, '$2y$12$91ULnXTRXA.AL77V0Apr.uVKmpRfFW4VmnpnAN01xaIuuSbfYIMJu', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:55:39', '2024-05-29 08:55:39'),
(124, 'บุคคอรีย์  มะหมัน', 's10058@jariyathum.ac.th', NULL, NULL, NULL, NULL, NULL, NULL, '18486893', '20758363', 'duueMC3ACI', 0, 0, 0, 0, NULL, NULL, '$2y$12$Tljkw3Yic/KK138kovOKr.7NYAHFM5BVjh7T5TLOzEsbaykksytlO', NULL, NULL, NULL, NULL, NULL, '2024-05-29 08:58:09', '2024-05-29 08:58:09'),
(125, 'อักมาล หว่าหลำ', 's10060@jariyathum.ac.th', '0325469871', NULL, NULL, NULL, NULL, NULL, '50824863', '98857356', '3DavyG2Sjs', 0, 0, 0, 0, NULL, NULL, '$2y$12$IFMXBUc/CgnRLcY7STFxMue.j51CCQihr.ZVSHM.qSqh6g3i/5.7y', NULL, NULL, NULL, NULL, NULL, '2024-05-29 09:00:54', '2024-05-29 09:00:54'),
(126, 'อรรถพล หลีเม๊าะ', 's10055@jariyathum.ac.th', '0661425602', NULL, NULL, NULL, NULL, NULL, '50824863', '89802007', 'YleNu4wKeg', 0, 0, 0, 0, NULL, NULL, '$2y$12$ob1QDCsCiIPAB0F/uAiEMO7D/GJBS3wBZzyuFqQdgDVx0SgSTpBWa', NULL, NULL, NULL, NULL, NULL, '2024-05-29 09:04:30', '2024-05-29 09:04:30'),
(127, 'สุริยะ แอเก็ม', 's10059@jariyathum.ac.th', '0299911211', NULL, NULL, NULL, NULL, NULL, '50824863', '62706681', '7e7WtWdqBJ', 0, 0, 0, 0, NULL, NULL, '$2y$12$mm2Dv/pfNSrBkuiRKYpQFO7u/VSYCHVND3TIPw1SK09EjQSON5Bku', NULL, NULL, NULL, NULL, NULL, '2024-05-29 09:06:25', '2024-05-29 09:06:25'),
(128, 'อัซฮาร์ หยีหีม', 's10056@jariyatham.ac.th', '0813462070', NULL, NULL, NULL, NULL, NULL, '50824863', '66269075', 'BmO2SwJj8W', 0, 0, 0, 0, NULL, NULL, '$2y$12$9yBu8ERzuaibKFPWJwiNb.fobOvh6IfWMdamYrksvrPfPkcRZUKPu', NULL, NULL, NULL, NULL, NULL, '2024-05-29 09:09:46', '2024-05-29 09:09:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_answer_questions`
--

DROP TABLE IF EXISTS `user_answer_questions`;
CREATE TABLE IF NOT EXISTS `user_answer_questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `answer_id` bigint UNSIGNED NOT NULL,
  `correct_option_id` bigint UNSIGNED NOT NULL,
  `points` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_answer_questions_user_id_foreign` (`user_id`),
  KEY `user_answer_questions_question_id_foreign` (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb3_unicode_ci,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `interests` text COLLATE utf8mb3_unicode_ci,
  `profile_picture` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `social_media_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `privacy_settings` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'public',
  `followers` bigint UNSIGNED NOT NULL DEFAULT '0',
  `following` bigint UNSIGNED NOT NULL DEFAULT '0',
  `friends` bigint UNSIGNED NOT NULL DEFAULT '0',
  `join_date` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_profiles_user_id_foreign` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `visitor_counters`
--

DROP TABLE IF EXISTS `visitor_counters`;
CREATE TABLE IF NOT EXISTS `visitor_counters` (
  `id` int NOT NULL,
  `visitor_counter` bigint NOT NULL DEFAULT '0',
  `login_counter` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_counters`
--

INSERT INTO `visitor_counters` (`id`, `visitor_counter`, `login_counter`, `created_at`, `updated_at`) VALUES
(1, 12293, 283, '2024-01-08 15:06:04', '2024-06-04 01:23:38');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
