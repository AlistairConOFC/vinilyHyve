(function() {
    function initZoom() {
        const coverImage = document.querySelector('.product-cover__image');
        if (!coverImage) return;
        
        const zoomLens = document.createElement('div');
        zoomLens.className = 'zoom-lens';
        zoomLens.style.cssText = `position:absolute;border:2px solid #d87c3c;width:150px;height:150px;border-radius:50%;cursor:crosshair;display:none;pointer-events:none;z-index:100;`;
        
        const zoomResult = document.createElement('div');
        zoomResult.className = 'zoom-result';
        zoomResult.style.cssText = `position:absolute;border:1px solid #2c2c2c;width:300px;height:300px;background-repeat:no-repeat;display:none;z-index:101;background-color:#1a1a1a;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.5);`;
        
        const wrapper = coverImage.parentElement;
        wrapper.style.position = 'relative';
        wrapper.appendChild(zoomLens);
        wrapper.appendChild(zoomResult);
        
        coverImage.addEventListener('mousemove', (e) => {
            const rect = coverImage.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            if (x < 0 || y < 0 || x > rect.width || y > rect.height) {
                zoomLens.style.display = 'none';
                zoomResult.style.display = 'none';
                return;
            }
            
            zoomLens.style.display = 'block';
            zoomResult.style.display = 'block';
            zoomLens.style.left = (x - zoomLens.offsetWidth / 2) + 'px';
            zoomLens.style.top = (y - zoomLens.offsetHeight / 2) + 'px';
            
            const percentX = (x / rect.width) * 100;
            const percentY = (y / rect.height) * 100;
            zoomResult.style.backgroundImage = `url(${coverImage.src})`;
            zoomResult.style.backgroundSize = `${coverImage.clientWidth * 2}px ${coverImage.clientHeight * 2}px`;
            zoomResult.style.backgroundPosition = `${percentX}% ${percentY}%`;
            
            let left = e.clientX + 20;
            let top = e.clientY - zoomResult.offsetHeight / 2;
            if (left + zoomResult.offsetWidth > window.innerWidth) left = e.clientX - zoomResult.offsetWidth - 20;
            if (top < 0) top = 10;
            zoomResult.style.left = left + 'px';
            zoomResult.style.top = top + 'px';
        });
        
        coverImage.addEventListener('mouseleave', () => {
            zoomLens.style.display = 'none';
            zoomResult.style.display = 'none';
        });
    }
    
    document.addEventListener('DOMContentLoaded', initZoom);
})();
