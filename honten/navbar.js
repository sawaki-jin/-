document.addEventListener('DOMContentLoaded', function() {
    const userInfo = document.querySelector('.user-info');
    const username = document.querySelector('.username');
    let modal = null;

    username.addEventListener('click', function(e) {
        e.stopPropagation();
        if (modal) {
            document.body.removeChild(modal);
            modal = null;
        } else {
            createModal();
        }
    });

    document.addEventListener('click', function() {
        if (modal) {
            document.body.removeChild(modal);
            modal = null;
        }
    });

    function createModal() {
        modal = document.createElement('div');
        modal.className = 'user-modal';
        modal.innerHTML = document.querySelector('.user-dropdown').innerHTML;
        
        const rect = username.getBoundingClientRect();
        modal.style.position = 'fixed';
        modal.style.top = (rect.bottom + window.scrollY + 5) + 'px'; // 5px下に移動
        modal.style.left = (rect.left - 10) + 'px'; // 10px左に移動
        modal.style.backgroundColor = 'white';
        modal.style.padding = '15px 20px'; // パディングを増加
        modal.style.border = '1px solid #ccc';
        modal.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
        modal.style.minWidth = '220px'; // 最小幅を設定

        document.body.appendChild(modal);

        modal.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(function(dropdown) {
        var dropdownContent = dropdown.querySelector('.dropdown-content');

        // 親項目の幅を取得し、ドロップダウンメニューに適用
        var parentWidth = dropdown.offsetWidth;
        dropdownContent.style.minWidth = parentWidth + 'px';
        
        dropdown.addEventListener('mouseover', function() {
            dropdownContent.style.display = 'block';
        });

        dropdown.addEventListener('mouseout', function() {
            dropdownContent.style.display = 'none';
        });
    });
});
