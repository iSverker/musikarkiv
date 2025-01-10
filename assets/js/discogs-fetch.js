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

// Make the function available globally
window.fetchSuggestions = fetchSuggestions;