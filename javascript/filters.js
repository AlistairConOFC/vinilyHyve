(function() {
    let debounceTimer;
    const catalogContainer = document.getElementById('catalog-container');
    const filterForm = document.getElementById('filter-form');
    
    function getFilters() {
        const genres = Array.from(document.querySelectorAll('input[name="genre"]:checked')).map(cb => cb.value);
        const countries = Array.from(document.querySelectorAll('input[name="country"]:checked')).map(cb => cb.value);
        const vinylTypes = Array.from(document.querySelectorAll('input[name="vinyl_type"]:checked')).map(cb => cb.value);
        
        const yearMin = document.getElementById('year-min')?.value;
        const yearMax = document.getElementById('year-max')?.value;
        const priceMin = document.getElementById('price-min')?.value;
        const priceMax = document.getElementById('price-max')?.value;
        
        return {
            genres,
            countries,
            vinyl_types: vinylTypes,
            year_min: yearMin && yearMin !== '' ? parseInt(yearMin) : null,
            year_max: yearMax && yearMax !== '' ? parseInt(yearMax) : null,
            price_min: priceMin && priceMin !== '' ? parseFloat(priceMin) : null,
            price_max: priceMax && priceMax !== '' ? parseFloat(priceMax) : null
        };
    }
    
    function applyFilters() {
        const filters = getFilters();
        
        if (catalogContainer) {
            catalogContainer.innerHTML = '<div class="loader" style="text-align:center;padding:40px;"><div class="spinner" style="width:40px;height:40px;border:3px solid #2c2c2c;border-top-color:#d87c3c;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto;"></div><p>Ищем пластинки...</p></div>';
        }
        
        fetch('/api/filter', {
            method: 'POST',
            headers: { 'Content-Type':application/json'},
            body: JSON.stringify(filters)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && catalogContainer) {
                catalogContainer.innerHTML = data.html;
                const resultCount = document.getElementById('result-count');
                if (resultCount) resultCount.textContent = `Найдено: ${data.count} релизов`;
            }
        })
        .catch(err => {
            console.error('Filter error:', err);
            if (catalogContainer) {
                catalogContainer.innerHTML = '<p style="color:#c44536;text-align:center;">Ошибка загрузки. Попробуйте позже.</p>';
            }
        });
    }
    
    function debouncedFilter() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 400);
    }
    
    if (filterForm) {
        filterForm.querySelectorAll('input, select').forEach(input => {
            if (input.type === 'range') {
                input.addEventListener('input', debouncedFilter);
            } else {
                input.addEventListener('change', debouncedFilter);
            }
        });
    }
    
    if (catalogContainer) applyFilters();
})();

const style = document.createElement('style');
style.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
document.head.appendChild(style);
