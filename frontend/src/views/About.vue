<template>
  <div class="about-page">
    <!-- Hero -->
    <div class="glass flex items-center" style="margin-top: 40px; padding: 60px; border-radius: 24px; gap: 60px; flex-wrap: wrap; margin-bottom: 50px;">
      <!-- Avatar -->
      <div style="flex: 1; min-width: 260px; text-align: center;">
        <div style="width: 200px; height: 200px; border-radius: 50%; margin: 0 auto; position: relative; background: linear-gradient(135deg, var(--color-accent), #f0c040); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 60px rgba(212,175,55,0.4);">
          <UserCheck :size="84" style="color: #050811;" />
        </div>
        <div class="flex justify-center gap-3 mt-6" style="flex-wrap: wrap;">
          <a href="https://portfolio-tau-inky-96i2vyeddb.vercel.app/" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; border: 1px solid var(--color-accent); color: var(--color-accent); font-weight: 700; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; background: rgba(212,175,55,0.1);">
            <Globe :size="16" /> Mon Portfolio
          </a>
          <a href="https://github.com/LeYASSOUNG" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; border: 1px solid var(--color-border); color: var(--color-text); text-decoration: none; font-size: 0.9rem; transition: all 0.2s;">
            <Github :size="16" /> GitHub
          </a>
          <a href="#" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; border: 1px solid var(--color-border); color: var(--color-text); text-decoration: none; font-size: 0.9rem; transition: all 0.2s;">
            <Linkedin :size="16" /> LinkedIn
          </a>
        </div>
      </div>
      <!-- Texte -->
      <div style="flex: 2; min-width: 300px;">
        <div style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.3); padding: 6px 16px; border-radius: 999px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--color-accent); margin-bottom: 16px; font-weight: 600;">
          <Sparkles :size="15" /> Développeur Full Stack & Créateur IA
        </div>
        <h1 class="mb-2" style="font-size: 2.2rem;">Diarrassouba Yassoungo Youssouf</h1>
        <h2 style="color: var(--color-accent); font-size: 1.1rem; font-weight: 600; margin-bottom: 20px;">Fondateur de Yass Digital Lab</h2>
        <p class="mb-4" style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-light);">
          Je suis un <strong style="color: var(--color-text);">développeur Full Stack</strong> et créateur de solutions numériques.
          À travers <strong style="color: var(--color-accent);">Yass Digital Lab</strong>, je conçois des outils numériques,
          des ressources éducatives et des solutions basées sur l'intelligence artificielle pour aider
          les particuliers et les entreprises à gagner en efficacité.
        </p>
        <p class="mb-8" style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-light);">
          Mon objectif est de simplifier l'accès aux technologies avancées et de fournir des produits
          de haute qualité (templates, packs de prompts, guides) qui font la différence dans votre travail quotidien.
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

    <!-- Compétences -->
    <section class="mb-12">
      <h2 class="text-center mb-8 flex items-center justify-center gap-3" style="font-size: 1.8rem;">
        <Wrench :size="26" style="color: var(--color-accent);" /> Mes Compétences
      </h2>
      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div v-for="skill in skills" :key="skill.name" class="glass text-center" style="padding: 24px; border-radius: 14px; transition: all 0.3s; border: 1px solid transparent;">
          <div style="display: flex; justify-content: center; align-items: center; height: 50px; margin-bottom: 10px; color: var(--color-accent);">
            <component :is="skill.iconComponent" :size="36" />
          </div>
          <h4 style="font-weight: 700; margin-bottom: 4px;">{{ skill.name }}</h4>
          <p style="font-size: 0.85rem; color: var(--color-text-light);">{{ skill.level }}</p>
          <div style="margin-top: 10px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 999px; overflow: hidden;">
            <div :style="`width: ${skill.percent}%; background: var(--color-accent); height: 100%; border-radius: 999px;`"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Parcours -->
    <section class="glass mb-12" style="padding: 50px 40px; border-radius: 24px;">
      <h2 class="mb-8 flex items-center gap-3" style="font-size: 1.8rem;">
        <Calendar :size="26" style="color: var(--color-accent);" /> Mon Parcours
      </h2>
      <div class="flex flex-col gap-6">
        <div v-for="item in timeline" :key="item.year" class="flex gap-6 items-start" style="padding-bottom: 24px; border-bottom: 1px solid var(--color-border);">
          <div style="background: rgba(212,175,55,0.15); color: var(--color-accent); padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; white-space: nowrap; border: 1px solid rgba(212,175,55,0.3); display: flex; align-items: center; gap: 6px;">
            <Clock :size="14" /> {{ item.year }}
          </div>
          <div>
            <h4 style="font-weight: 700; margin-bottom: 4px;">{{ item.title }}</h4>
            <p style="color: var(--color-text-light); font-size: 0.95rem;">{{ item.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="glass text-center" style="padding: 60px 20px; border-radius: 24px; background: linear-gradient(135deg, rgba(212,175,55,0.1), rgba(15,23,42,0.4));">
      <h2 class="mb-4 flex items-center justify-center gap-3" style="font-size: 1.8rem;">
        Travaillons ensemble <Rocket :size="26" style="color: var(--color-accent);" />
      </h2>
      <p style="color: var(--color-text-light); margin-bottom: 30px; max-width: 500px; margin-inline: auto;">Vous avez un projet ? Je suis disponible pour des missions en freelance et des collaborations.</p>
      <router-link to="/contact" class="btn btn-primary" style="font-size: 1.05rem; padding: 0.8rem 2rem; display: inline-flex; align-items: center; gap: 8px;">
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
  { year: '2026–Present', title: 'Fondateur — Yass Digital Lab', description: 'Création de la plateforme de vente de produits numériques, développement d\'outils IA et de templates premium.' },
  { year: '2026–Present', title: 'Développeur Full Stack Freelance', description: 'Conception et développement d\'applications web pour clients en Afrique et en Europe. Stack : Laravel, Vue.js, PostgreSQL.' },
  { year: '2023–2026', title: 'Formation — Développement Web', description: 'Maîtrise des technologies modernes du web (PHP, JavaScript, SQL) et des bonnes pratiques de développement logiciel.' },
];
</script>

<style scoped>
.glass:hover { border-color: rgba(212,175,55,0.3) !important; }
</style>

