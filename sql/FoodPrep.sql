-- FoodPrep Database Schema
-- Food Preparation Web Application
-- Tags are implemented via a junction table for scalability and reusability
-- Weekly meal planning with automatic shopping list generation

-- ===========================
-- USERS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    dietary_preferences TEXT,
    allergies TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email)
);

-- ===========================
-- CATEGORIES TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===========================
-- TAGS TABLE (for meal tagging)
-- ===========================
CREATE TABLE IF NOT EXISTS tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===========================
-- RECIPES/MEALS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS recipes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructions TEXT,
    image_url VARCHAR(255),
    prep_time INT COMMENT 'in minutes',
    cook_time INT COMMENT 'in minutes',
    servings INT DEFAULT 1,
    difficulty VARCHAR(50),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX (category_id),
    INDEX (created_at)
);

-- ===========================
-- RECIPE_TAGS JUNCTION TABLE
-- ===========================
-- This junction table implements the many-to-many relationship
-- A recipe can have multiple tags, and tags are reusable across recipes
CREATE TABLE IF NOT EXISTS recipe_tags (
    recipe_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (recipe_id, tag_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- ===========================
-- INGREDIENTS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS ingredients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    calories DECIMAL(8, 2),
    protein DECIMAL(8, 2) COMMENT 'in grams',
    carbs DECIMAL(8, 2) COMMENT 'in grams',
    fat DECIMAL(8, 2) COMMENT 'in grams',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===========================
-- RECIPE_INGREDIENTS JUNCTION TABLE
-- ===========================
-- This junction table implements the many-to-many relationship
-- A recipe can use multiple ingredients, and ingredients can be used in multiple recipes
CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- ===========================
-- WEEKLY_PLANS TABLE
-- ===========================
-- Stores each user's weekly meal plans
CREATE TABLE IF NOT EXISTS weekly_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    week_start_date DATE NOT NULL,
    number_of_servings INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (week_start_date),
    UNIQUE KEY unique_user_week (user_id, week_start_date)
);

-- ===========================
-- WEEKLY_PLAN_ITEMS TABLE
-- ===========================
-- Links meals to specific days in the weekly plan
CREATE TABLE IF NOT EXISTS weekly_plan_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    weekly_plan_id INT NOT NULL,
    recipe_id INT NOT NULL,
    day_of_week INT NOT NULL COMMENT '1=Monday, 7=Sunday',
    meal_type VARCHAR(50) COMMENT 'breakfast, lunch, dinner, snack',
    servings INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (weekly_plan_id) REFERENCES weekly_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    INDEX (weekly_plan_id),
    INDEX (recipe_id)
);

-- ===========================
-- SHOPPING_LISTS TABLE
-- ===========================
-- Generated shopping lists based on weekly meal plans
CREATE TABLE IF NOT EXISTS shopping_lists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    weekly_plan_id INT NOT NULL,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (weekly_plan_id) REFERENCES weekly_plans(id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (weekly_plan_id)
);

-- ===========================
-- SHOPPING_LIST_ITEMS TABLE
-- ===========================
-- Individual items in the shopping list
CREATE TABLE IF NOT EXISTS shopping_list_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shopping_list_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    is_checked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shopping_list_id) REFERENCES shopping_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE,
    INDEX (shopping_list_id),
    INDEX (ingredient_id)
);
