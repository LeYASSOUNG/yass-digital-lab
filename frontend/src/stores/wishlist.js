import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref(JSON.parse(localStorage.getItem('wishlist')) || []);

  const toggleWishlist = (product) => {
    const idx = items.value.findIndex(p => p.id === product.id);
    if (idx > -1) {
      items.value.splice(idx, 1);
    } else {
      items.value.push(product);
    }
    localStorage.setItem('wishlist', JSON.stringify(items.value));
  };

  const isFavorite = (productId) => {
    return items.value.some(p => p.id === productId);
  };

  return { items, toggleWishlist, isFavorite };
});
