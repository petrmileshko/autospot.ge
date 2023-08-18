const blocksToShow = 8;
  let isShowingAll = false;

  const toggleBlocks = () => {
    const blocks = document.querySelectorAll('.toggle-list-optionst');

    blocks.forEach((block, index) => {
      if (isShowingAll || index < blocksToShow) {
        block.style.display = 'block';
      } else {
        block.style.display = 'none';
      }
    });
  };

  const showMoreButton = document.querySelector('.catalog-more-filter-show');
  const hideButton = document.querySelector('.catalog-more-filter-hide');

  showMoreButton.addEventListener('click', function() {
    isShowingAll = true;
    toggleBlocks();
  });

  hideButton.addEventListener('click', function() {
    isShowingAll = false;
    toggleBlocks();
  });

  toggleBlocks(); // Initial display of blocks