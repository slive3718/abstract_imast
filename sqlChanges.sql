-- ####### SQL changes ######


-- --------------------------------------------------------

--
-- Table structure for table `abstract_learning_objectives`
--

CREATE TABLE `abstract_learning_objectives` (
  `id` int(11) NOT NULL,
  `abstract_id` int(11) NOT NULL,
  `objective_1` text NOT NULL,
  `objective_2` text NOT NULL,
  `objective_3` text NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abstract_learning_objectives`
--
ALTER TABLE `abstract_learning_objectives`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abstract_learning_objectives`
--
ALTER TABLE `abstract_learning_objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


-- --------------------------------------------------------
ALTER TABLE `abstracts`
CHANGE `title` `title` VARCHAR(555) DEFAULT NULL,
CHANGE `session_type` `session_type` VARCHAR(255) DEFAULT NULL,
CHANGE `basic_science_proposal_format` `substance_area` VARCHAR(255) DEFAULT NULL,
CHANGE `abstract_category_id` `population` VARCHAR(255) DEFAULT NULL,
CHANGE `abstract_body` `abstract_brief_summary` LONGTEXT DEFAULT NULL,
CHANGE `summary` `abstract_text` LONGTEXT DEFAULT NULL,
CHANGE `hypothesis` `instructional_meth` LONGTEXT DEFAULT NULL,
CHANGE `study_design` `audio_visual_needs` LONGTEXT DEFAULT NULL,
DROP `conclusion`,
DROP `results`,
DROP `introduction`,
DROP `methods`,
DROP `image_caption`,
DROP `take_home_message`,
CHANGE `fda_unapproved_uses` `fda_unapproved_uses` TINYINT(1) DEFAULT NULL,
CHANGE `discuss_product_name` `discuss_product_name` TINYINT(1) DEFAULT NULL,
CHANGE `is_fda_accepted` `is_fda_accepted` TINYINT(1) DEFAULT NULL;


Alter TABLE `author_users_details`  ADD COLUMN(
 
  `head_shot_upload_name` varchar(255) NOT NULL,
  `head_shot_save_path` varchar(555) NOT NULL,
  `head_shot_rand_name` varchar(555) NOT NULL,
  `head_shot_file_path` varchar(555) NOT NULL
) 


ALTER TABLE abstracts
ADD COLUMN agree_to_register INT NOT NULL;


-- ######## Create Abstract Topic #####

CREATE TABLE `abstract_topics` (
  `id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `abstract_topics`
--

INSERT INTO `abstract_topics` (`id`, `value`, `name`) VALUES
(1, 1, 'ACL'),
(2, 2, 'Adaptive Stress'),
(3, 3, 'Bone Stress'),
(4, 4, 'Concussion'),
(5, 5, 'Elbow'),
(6, 6, 'Female Athlete'),
(7, 7, 'Foot/Ankle'),
(8, 8, 'Hip Disorders'),
(9, 9, 'Injury Prevention'),
(10, 10, 'Meniscus'),
(11, 11, 'Mental Health'),
(12, 12, 'Motion Analysis'),
(13, 13, 'Multi-Ligament Knee'),
(14, 14, 'OCD Knee'),
(15, 15, 'OCD Elbow'),
(16, 16, 'Opiates'),
(17, 17, 'Outcome Measures'),
(18, 18, 'Patellofemoral Instability'),
(19, 19, 'Rehabilitation'),
(20, 20, 'Shoulder Instability'),
(21, 21, 'Sleep'),
(22, 22, 'Spine Spondylolysis'),
(23, 23, 'Sports Specialization'),
(24, 24, 'Tibial Spine'),
(25, 25, 'Ultrasound'),
(26, 26, 'Other');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abstract_topics`
--
ALTER TABLE `abstract_topics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abstract_topics`
--
ALTER TABLE `abstract_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;


--  ####### End abstract topic ##########

ALTER TABLE `abstracts` CHANGE `background` `background` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `hypothesis` `hypothesis` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `methods` `methods` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `results` `results` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `conclusion` `conclusion` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `reference` `reference` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `mail_logs`
ADD COLUMN `unique_code` VARCHAR(255) NOT NULL AFTER `id`,
ADD COLUMN `recipient_type` VARCHAR(255) NOT NULL,
ADD COLUMN `recipient_group` VARCHAR(255) NOT NULL,
ADD COLUMN `is_test` INT(11) NULL,
ADD COLUMN `total_recipients` INT(11) NOT NULL;


-- Add Presentation Preferences table
CREATE TABLE `presentation_preferences` (
`id` int(11) NOT NULL,
`preference_id` int(11) NOT NULL,
`value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `presentation_preferences` (`id`, `preference_id`, `value`) VALUES
(1, 1, 'Presentation Only'),
(2, 2, 'Publication Only'),
(3, 3, 'Presentation and publication');
ALTER TABLE `presentation_preferences`
    ADD PRIMARY KEY (`id`);
ALTER TABLE `presentation_preferences`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
-- End Presentation Preferences table