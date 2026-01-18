# FoodPrepper API Documentation

## Base URL
```
http://localhost
```

## Endpoints

### 1. API Documentation
Get information about all available endpoints.

**Endpoint:** `GET /api/index`

**Response:**
```json
{
    "success": true,
    "message": "FoodPrepper API v1.0",
    "endpoints": [...]
}
```

---

### 2. Get All Recipes
Retrieve all recipes with categories, prep time, and basic information.

**Endpoint:** `GET /api/recipes`

**Response:**
```json
{
    "success": true,
    "count": 17,
    "data": [
        {
            "id": "1",
            "title": "Avocado Toast",
            "description": "Simple and healthy breakfast option",
            "prep_time": "5",
            "cook_time": "5",
            "servings": "2",
            "categories": [
                {
                    "name": "Breakfast",
                    "color": "#FFB347",
                    "icon": "☀️"
                }
            ]
        }
    ]
}
```

---

### 3. Get Single Recipe
Retrieve a single recipe with full details including ingredients and instructions.

**Endpoint:** `GET /api/recipe?id={id}`

**Parameters:**
- `id` (required) - Recipe ID

**Example:** `GET /api/recipe?id=1`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": "1",
        "title": "Avocado Toast",
        "description": "...",
        "instructions": "...",
        "ingredients": [
            {
                "name": "Bread",
                "quantity": "2",
                "unit": "slices"
            }
        ],
        "categories": [...],
        "tags": [...]
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "error": "Recipe not found"
}
```

---

### 4. Get All Ingredients
Retrieve all ingredients with nutritional information.

**Endpoint:** `GET /api/ingredients`

**Response:**
```json
{
    "success": true,
    "count": 51,
    "data": [
        {
            "id": "1",
            "name": "Onion",
            "calories": "40.00",
            "protein": "1.10",
            "carbs": "9.30",
            "fat": "0.10"
        }
    ]
}
```

---

### 5. Get Shopping List
Retrieve a shopping list with all items and completion progress.

**Endpoint:** `GET /api/shoppinglist?id={id}`

**Parameters:**
- `id` (required) - Shopping list ID

**Example:** `GET /api/shoppinglist?id=1`

**Response:**
```json
{
    "success": true,
    "data": {
        "shopping_list": {
            "id": "1",
            "user_id": "1",
            "weekly_plan_id": "1",
            "generated_date": "2026-01-18"
        },
        "items": [
            {
                "id": "1",
                "ingredient_name": "Chicken Breast",
                "quantity": "500.00",
                "unit": "grams",
                "is_checked": "0"
            }
        ],
        "progress": {
            "total": 10,
            "checked": 3,
            "percentage": 30
        }
    }
}
```

**Error Response (400):**
```json
{
    "success": false,
    "error": "Shopping list ID is required. Use ?id=123"
}
```

---

## Response Format

All endpoints return JSON with the following structure:

**Success:**
```json
{
    "success": true,
    "count": 10,           // Optional: for collections
    "data": {...}          // Response payload
}
```

**Error:**
```json
{
    "success": false,
    "error": "Error message"
}
```

## HTTP Status Codes

- `200 OK` - Request successful
- `400 Bad Request` - Invalid parameters
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server error

## CORS

The API includes CORS headers to allow cross-origin requests:
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, OPTIONS
Access-Control-Allow-Headers: Content-Type
```

## Testing

### Using cURL
```bash
# Get all recipes
curl http://localhost/api/recipes

# Get single recipe
curl http://localhost/api/recipe?id=1

# Get ingredients
curl http://localhost/api/ingredients

# Get shopping list
curl http://localhost/api/shoppinglist?id=1
```

### Using PowerShell
```powershell
# Get all recipes
Invoke-WebRequest -Uri "http://localhost/api/recipes" | 
    Select-Object -ExpandProperty Content | ConvertFrom-Json

# Get single recipe
Invoke-WebRequest -Uri "http://localhost/api/recipe?id=1" | 
    Select-Object -ExpandProperty Content | ConvertFrom-Json
```

### Using JavaScript (fetch)
```javascript
// Get all recipes
fetch('http://localhost/api/recipes')
    .then(response => response.json())
    .then(data => console.log(data));

// Get single recipe
fetch('http://localhost/api/recipe?id=1')
    .then(response => response.json())
    .then(data => console.log(data.data));
```

## Notes

- All responses include pretty-printed JSON for readability
- All text is UTF-8 encoded with `JSON_UNESCAPED_UNICODE`
- No authentication required (public API)
- GET requests only (read-only API)

---

**Version:** 1.0  
**Last Updated:** January 18, 2026
