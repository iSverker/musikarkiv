document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="sok"]');
    if (!searchInput) {
        return; // Exit if the search input element is not found
    }

    const dropdown = document.createElement('div');
    dropdown.className = 'search-dropdown';
    searchInput.parentNode.insertBefore(dropdown, searchInput.nextSibling);

    let currentIndex = -1;

    /**
     * Handles the input event on the search input field.
     */
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();
        if (query.length > 2) {
            fetchSuggestions(query, dropdown);
        } else {
            dropdown.innerHTML = '';
            dropdown.style.display = 'none';
        }
    });

    /**
     * Handles the keydown event on the search input field.
     */
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.search-dropdown-item');
        if (e.key === 'ArrowDown') {
            currentIndex = (currentIndex + 1) % items.length;
            updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            updateActiveItem(items);
        } else if (e.key === 'Enter') {
            if (currentIndex >= 0) {
                const currentUrl = new URL(window.location.href);
                const baseUrl = currentUrl.pathname.endsWith('/') ? currentUrl.pathname.slice(0, -1) : currentUrl.pathname;
                const itemInfoUrl = new URL('item-info', currentUrl.origin + baseUrl);
                itemInfoUrl.searchParams.set('id', items[currentIndex].dataset.itemId);
                window.location.href = itemInfoUrl.toString();
                e.preventDefault();
            } else {
                searchInput.form.submit();
            }
        }
    });

    /**
     * Displays the search results in the dropdown.
     * @param {Array} results - The search results.
     */
    function displayResults(results) {
        dropdown.innerHTML = '';
        if (results.length > 0) {
            results.forEach(result => {
                if (result.title.trim() !== '') {
                    const item = document.createElement('div');
                    item.classList.add('search-dropdown-item');
                    item.textContent = `${result.artist} - ${result.title}`;
                    item.dataset.itemId = result.id;
                    item.addEventListener('click', function() {
                        const currentUrl = new URL(window.location.href);
                        const baseUrl = currentUrl.pathname.endsWith('/') ? currentUrl.pathname.slice(0, -1) : currentUrl.pathname;
                        const itemInfoUrl = new URL('item-info', currentUrl.origin + baseUrl);
                        itemInfoUrl.searchParams.set('id', item.dataset.itemId);
                        window.location.href = itemInfoUrl.toString();
                    });
                    dropdown.appendChild(item);
                }
            });
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    /**
     * Updates the active item in the dropdown.
     * @param {NodeList} items - The list of dropdown items.
     */
    function updateActiveItem(items) {
        items.forEach(item => item.classList.remove('active'));
        if (currentIndex >= 0) {
            items[currentIndex].classList.add('active');
            items[currentIndex].scrollIntoView({ block: 'nearest' });
        }
    }

});

/**
 * Fetches search suggestions from the server.
 * @param {string} query - The search query.
 * @param {HTMLElement} dropdown - The dropdown element to display suggestions.
 */
function fetchSuggestions(query, dropdown) {
    fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            dropdown.innerHTML = '';
            data.suggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'search-dropdown-item';
                item.textContent = suggestion;
                dropdown.appendChild(item);
            });
        });
}
