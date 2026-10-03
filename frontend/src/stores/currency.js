import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCurrencyStore = defineStore('currency', () => {
  // Monnaie courante forcée sur FCFA
  const currentCurrency = ref('XOF');

  const setCurrency = (curr) => {
    // Ne fait rien, on force XOF
  };

  const toggleCurrency = () => {
    // Ne fait rien, on force XOF
  };

  /**
   * Formate un montant en FCFA.
   */
  const format = (amount) => {
    const val = parseFloat(amount) || 0;
    return `${val.toLocaleString('fr-FR')} FCFA`;
  };

  /**
   * Retourne l'affichage FCFA.
   */
  const formatDual = (amount) => {
    return format(amount);
  };

  return {
    currentCurrency,
    setCurrency,
    toggleCurrency,
    format,
    formatDual
  };
});

