<?php $base_url = dirname($_SERVER['SCRIPT_NAME'], 2) . '/'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Service Categories</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <style>
    .scroll-container::-webkit-scrollbar {
      height: 6px;
    }
    .scroll-container::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 9999px;
    }
    .tablinks.tab-active {
      background-color: #e0e7ff;
      color: #4f46e5;
    }
    .dropdown-panel {
      display: none;
      position: absolute;
      left: 0;
      top: 100%;
      z-index: 50;
      width: max-content;
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 1rem;
      min-width: 250px;
    }
    .tab-item:hover .dropdown-panel {
      display: block;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 p-6 relative">

  <h1 class="text-3xl font-bold text-indigo-600 mb-6">Browse Services</h1>

  <div class="flex-1 ">
    <div class="tab flex space-x-2 px-1 py-2 whitespace-nowrap min-w-max">
      <?php if (!empty($categories)) : ?>

        <!-- All Categories Tab -->
        <div class="tab-item relative group">
          <button 
            class="tablinks px-5 py-2 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" 
            data-category="All Categories"
            data-category-id="all">
            All Categories
          </button>
        </div>

        <!-- Loop through actual categories -->
        <?php foreach ($categories as $category): ?>
          <div class="tab-item relative group">
            <button 
              class="tablinks px-5 py-2 rounded-lg font-medium text-gray-600 hover:bg-gray-100 transition-colors" 
              data-category="<?= htmlspecialchars($category['name']) ?>"
              data-category-id="<?= htmlspecialchars($category['id']) ?>">
              <?= htmlspecialchars($category['name']) ?>
            </button>

            <!-- Dropdown container for subcategories -->
            <div class="dropdown-panel" data-dropdown="<?= htmlspecialchars($category['id']) ?>">
              <p class="text-sm text-gray-400">Loading...</p>
            </div>
          </div>
        <?php endforeach; ?>
        
      <?php else: ?>
        <p class="text-gray-400">No categories found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const BASE_URL = "<?= $base_url ?>";

    document.querySelectorAll('.tab-item').forEach(item => {
      const button = item.querySelector('.tablinks');
      const dropdown = item.querySelector('.dropdown-panel');
      const categoryId = button.dataset.categoryId;

      if (categoryId === 'all') return;

      item.addEventListener('mouseenter', () => {
        if (!dropdown || dropdown.dataset.loaded) return;

        fetch(BASE_URL + 'controllers/routes.php?action=displaySubCategory', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ category_id: categoryId })
        })
        .then(tite => tite.json())
        .then(pepe => {
          if (pepe.success && pepe.data.length > 0) {
            dropdown.innerHTML = pepe.data.map(pinasok => `
              <div class="py-1 px-2 hover:bg-gray-100 rounded text-sm text-gray-700 cursor-pointer">
                ${pinasok.name}
              </div>
            `).join('');
          } else {
            dropdown.innerHTML = `<p class="text-sm text-gray-400">No subcategories found.</p>`;
          }
          dropdown.dataset.loaded = "true";
        })
        .catch(() => {
          dropdown.innerHTML = `<p class="text-sm text-red-500">Error loading subcategories.</p>`;
        });
      });
    });
  </script>

</body>
</html>


