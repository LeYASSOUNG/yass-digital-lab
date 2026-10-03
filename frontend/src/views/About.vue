<template>
  <div class="about-page">
    <!-- Hero / Profile Spotlight -->
    <div class="glass flex items-center fade-in" style="margin-top: 30px; padding: 55px 45px; border-radius: var(--radius-lg); gap: 50px; flex-wrap: wrap; margin-bottom: 50px; position: relative; overflow: hidden;">
      <div class="hero-glow"></div>
      
      <!-- Avatar -->
      <div style="flex: 1; min-width: 250px; text-align: center; position: relative; z-index: 2;">
        <div style="width: 200px; height: 200px; border-radius: 50%; margin: 0 auto; position: relative; background: linear-gradient(135deg, #7C3AED, #06B6D4); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(124,58,237,0.4);">
          <UserCheck :size="84" style="color: var(--color-text);" />
        </div>
        <div class="flex justify-center gap-2.5 mt-6" style="flex-wrap: wrap;">
          <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: var(--radius-pill); border: 1px solid var(--color-primary); color: var(--color-primary); font-weight: 700; text-decoration: none; font-size: 0.88rem; background: rgba(124,58,237,0.1);">
            <Globe :size="15" /> Mon Portfolio
          </a>
          <a href="https://github.com/LeYASSOUNG" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: var(--radius-pill); border: 1px solid var(--color-border); color: var(--color-text); text-decoration: none; font-size: 0.88rem;">
            <Github :size="15" /> GitHub
          </a>
          <a href="#" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: var(--radius-pill); border: 1px solid var(--color-border); color: var(--color-text); text-decoration: none; font-size: 0.88rem;">
            <Linkedin :size="15" /> LinkedIn
          </a>
        </div>
      </div>

      <!-- Text Content -->
      <div style="flex: 2; min-width: 300px; position: relative; z-index: 2;">
        <span class="badge-pill badge-indigo mb-3 flex items-center gap-1.5" style="display: inline-flex;">
          <Sparkles :size="14" /> DÉVELOPPEUR FULL STACK & CRÉATEUR IA
        </span>
        <h1 class="mb-2" style="font-size: 2.3rem;">Diarrassouba Yassoungo Youssouf</h1>
        <h2 style="color: var(--color-primary); font-size: 1.15rem; font-weight: 700; margin-bottom: 20px;">Fondateur de Yass Digital Lab</h2>
        <p class="mb-4" style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-light);">
          Je suis un <strong style="color: var(--color-text);">développeur Full Stack</strong> et créateur de solutions numériques.
          À travers <strong style="color: var(--color-primary);">Yass Digital Lab</strong>, je conçois des outils numériques,
          des templates SaaS starter et des agents d'intelligence artificielle pour aider
          les professionnels et entreprises à accélérer leur croissance.
        </p>
        <p class="mb-8" style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-light);">
          Mon objectif est d'offrir des produits digitaux ultra-performants et prêts à l'emploi qui font gagner des semaines de travail.
        </p>
        <div class="flex gap-3" style="flex-wrap: wrap;">
          <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" class="btn btn-primary flex items-center gap-2">
            <Globe :size="18" /> Visiter mon Portfolio
          </a>
          <router-link to="/contact" class="btn btn-secondary flex items-center gap-2">
            <Mail :size="18" /> Me contacter
          </router-link>
          <router-link to="/services" class="btn btn-secondary flex items-center gap-2">
            <Briefcase :size="18" /> Mes services
          </router-link>
        </div>
      </div>
    </div>

    <!-- Compétences & Stack -->
    <section class="mb-12">
      <h2 class="text-center mb-8 flex items-center justify-center gap-3">
        <Wrench :size="28" style="color: var(--color-primary);" /> Stack Technique & Compétences
      </h2>
      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 20px;">
        <div v-for="skill in skills" :key="skill.name" class="glass glass-hover text-center" style="padding: 24px 18px; border-radius: var(--radius-md);">
          <div style="display: flex; justify-content: center; align-items: center; height: 50px; margin-bottom: 12px; color: var(--color-primary);">
            <component :is="skill.iconComponent" :size="36" />
          </div>
          <h4 style="font-weight: 700; margin-bottom: 4px;">{{ skill.name }}</h4>
          <p style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">{{ skill.level }}</p>
          <div style="margin-top: 12px; height: 5px; background: rgba(124,58,237,0.15); border-radius: 999px; overflow: hidden;">
            <div :style="`width: ${skill.percent}%; background: linear-gradient(90deg, #7C3AED, #06B6D4); height: 100%; border-radius: 999px;`"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Parcours -->
    <section class="glass mb-12" style="padding: 48px 38px; border-radius: var(--radius-lg);">
      <h2 class="mb-8 flex items-center gap-3">
        <Calendar :size="28" style="color: var(--color-primary);" /> Parcours & Réalisations
      </h2>
      <div class="flex flex-col gap-6">
        <div v-for="item in timeline" :key="item.year" class="flex gap-6 items-start" style="padding-bottom: 22px; border-bottom: 1px solid var(--color-border);">
          <div class="badge-pill badge-indigo" style="font-weight: 700; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
            <Clock :size="14" /> {{ item.year }}
          </div>
          <div>
            <h4 style="font-weight: 700; margin-bottom: 4px; font-size: 1.1rem;">{{ item.title }}</h4>
            <p style="color: var(--color-text-light); font-size: 0.95rem; line-height: 1.6;">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Contact -->
    <section class="glass text-center" style="padding: 60px 24px; border-radius: var(--radius-lg); background: linear-gradient(135deg, rgba(124,58,237,0.1), rgba(6,182,212,0.1));">
      <h2 class="mb-4 flex items-center justify-center gap-3">
        Travaillons ensemble <Rocket :size="28" style="color: var(--color-primary);" />
      </h2>
      <p style="color: var(--color-text-light); margin-bottom: 30px; max-width: 540px; margin-inline: auto; font-size: 1.05rem;">
        Vous avez un projet d'application web, d'automatisation ou d'IA ? Je suis disponible pour des missions et collaborations.
      </p>
      <router-link to="/contact" class="btn btn-primary" style="font-size: 1.05rem; padding: 0.85rem 2.4rem; display: inline-flex; align-items: center; gap: 8px;">
        <Mail :size="18" /> Prendre contact
      </router-link>
    </section>
  </div>
</template>

<script setup>
import { 
  UserCheck, 
  Globe, 
  Github, 
  Linkedin, 
  Sparkles, 
  Mail, 
  Briefcase, 
  Wrench, 
  Calendar, 
  Clock, 
  Rocket, 
  Server, 
  Code2, 
  Database, 
  Brain, 
  Terminal, 
  FileCode, 
  Container, 
  GitBranch 
} from 'lucide-vue-next';

const skills = [
  { name: 'Laravel', iconComponent: Server, level: 'Avancé', percent: 90 },
  { name: 'Vue.js', iconComponent: Code2, level: 'Avancé', percent: 85 },
  { name: 'PostgreSQL', iconComponent: Database, level: 'Intermédiaire', percent: 75 },
  { name: 'IA / Prompts', iconComponent: Brain, level: 'Expert', percent: 95 },
  { name: 'PHP', iconComponent: Terminal, level: 'Avancé', percent: 88 },
  { name: 'JavaScript', iconComponent: FileCode, level: 'Avancé', percent: 85 },
  { name: 'Docker', iconComponent: Container, level: 'Intermédiaire', percent: 65 },
  { name: 'Git', iconComponent: GitBranch, level: 'Avancé', percent: 90 },
];

const timeline = [
  { year: '2026âFCFA“Present', title: 'Fondateur — Yass Digital Lab', description: 'Création de la plateforme de vente de produits numériques, développement d\'outils IA et de templates premium.' },
  { year: '2026âFCFA“Present', title: 'Développeur Full Stack Freelance', description: 'Conception et développement d\'applications web pour clients en Afrique et en Europe. Stack : Laravel, Vue.js, PostgreSQL.' },
  { year: '2023âFCFA“2026', title: 'Formation — Développement Web', description: 'Maîtrise des technologies modernes du web (PHP, JavaScript, SQL) et des bonnes pratiques de développement logiciel.' },
];
</script>

<style scoped>
.about-page {
  background: var(--page-gradient);
  padding: 30px 0 80px;
  color: var(--color-text);
}
.about-page h1, .about-page h2, .about-page h4 {
  color: var(--color-text) !important;
}
.about-page p {
  color: var(--color-text-muted) !important;
}
</style>

