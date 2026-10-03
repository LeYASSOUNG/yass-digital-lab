<template>
  <div @click.self="$emit('close')" style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(12px);">
    <div class="glass fade-in-scale" style="max-width: 520px; width: 100%; border-radius: 24px; padding: 36px; position: relative; background: #0b0f19; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 80px rgba(0,0,0,0.8);">
      <button @click="$emit('close')" style="position: absolute; top: 18px; right: 18px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); cursor: pointer; color: #94A3B8; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;" title="Fermer">
        <X :size="16" />
      </button>

      <div class="flex items-center gap-3 mb-4">
        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center; color: #10B981;">
          <ShieldCheck :size="22" />
        </div>
        <div>
          <h3 style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; margin: 0 0 2px;">État des Services Yass Lab</h3>
          <p style="color: #94A3B8; font-size: 0.82rem; margin: 0;">Disponibilité Globale : 99.98% • En direct</p>
        </div>
      </div>

      <div class="flex flex-col gap-3 my-6">
        <div class="flex justify-between items-center p-3" style="background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
          <div class="flex items-center gap-2.5">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
            <span style="font-size: 0.88rem; font-weight: 700; color: #E2E8F0;">API Backend Laravel & REST</span>
          </div>
          <span style="font-size: 0.78rem; font-weight: 800; color: #10B981; background: rgba(16,185,129,0.15); padding: 2px 8px; border-radius: 999px;">Opérationnel (42ms)</span>
        </div>

        <div class="flex justify-between items-center p-3" style="background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
          <div class="flex items-center gap-2.5">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
            <span style="font-size: 0.88rem; font-weight: 700; color: #E2E8F0;">Moteur d'Agents IA (OpenAI / Claude)</span>
          </div>
          <span style="font-size: 0.78rem; font-weight: 800; color: #10B981; background: rgba(16,185,129,0.15); padding: 2px 8px; border-radius: 999px;">Actif (GPT-4o)</span>
        </div>

        <div class="flex justify-between items-center p-3" style="background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
          <div class="flex items-center gap-2.5">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
            <span style="font-size: 0.88rem; font-weight: 700; color: #E2E8F0;">Passerelles de Paiement (Wave / Stripe)</span>
          </div>
          <span style="font-size: 0.78rem; font-weight: 800; color: #10B981; background: rgba(16,185,129,0.15); padding: 2px 8px; border-radius: 999px;">100% Prêtes</span>
        </div>

        <div class="flex justify-between items-center p-3" style="background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.06);">
          <div class="flex items-center gap-2.5">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
            <span style="font-size: 0.88rem; font-weight: 700; color: #E2E8F0;">Serveur de Fichiers & Téléchargements</span>
          </div>
          <span style="font-size: 0.78rem; font-weight: 800; color: #10B981; background: rgba(16,185,129,0.15); padding: 2px 8px; border-radius: 999px;">En ligne (SSL)</span>
        </div>
      </div>

      <button @click="$emit('close')" class="btn btn-primary" style="width: 100%; padding: 10px; font-weight: 800; font-size: 0.88rem;">
        Fermer le diagnostic
      </button>
    </div>
  </div>
</template>

<script setup>
import { X, ShieldCheck } from 'lucide-vue-next';
defineEmits(['close']);
</script>
