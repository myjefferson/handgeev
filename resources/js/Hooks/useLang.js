import { usePage } from "@inertiajs/react";

export default function useLang(namespace = null) {
  const { props } = usePage();
  
  // Busca primeiro no props.lang (específico da página via Inertia::render)
  // Depois no props.translations (compartilhado globalmente via Inertia::share)
  const pageLang = props?.lang || {};
  const globalTranslations = props?.translations || {};

  const __ = (path, fallback = null) => {
    let fullPath;
    let searchTarget;

    if (namespace) {
      fullPath = `${namespace}.${path}`;
      
      // Primeiro busca no pageLang (específico da página)
      const pageValue = fullPath.split('.').reduce((obj, key) => obj?.[key], pageLang);
      if (pageValue !== undefined) return pageValue;
      
      // Se não encontrou, busca no globalTranslations
      searchTarget = globalTranslations;
    } else {
      fullPath = path;
      
      // Primeiro tenta no pageLang sem namespace
      const pageValue = fullPath.split('.').reduce((obj, key) => obj?.[key], pageLang);
      if (pageValue !== undefined) return pageValue;
      
      // Se não encontrou, busca no globalTranslations
      searchTarget = globalTranslations;
      
      // Tenta detectar namespace automático se houver apenas uma chave no pageLang
      const pageKeys = Object.keys(pageLang);
      if (pageKeys.length === 1) {
        const autoPath = `${pageKeys[0]}.${path}`;
        const autoValue = autoPath.split('.').reduce((obj, key) => obj?.[key], pageLang);
        if (autoValue !== undefined) return autoValue;
      }
    }

    // Busca no target (globalTranslations ou pageLang)
    const value = fullPath.split('.').reduce((obj, key) => obj?.[key], searchTarget);

    // Retorna o valor encontrado, ou fallback, ou o caminho literal
    return value ?? fallback ?? fullPath;
  };

  /**
   * Retorna o namespace completo para uso direto
   */
  const getNamespace = () => {
    if (namespace) {
      // Prioriza pageLang, depois globalTranslations
      return pageLang[namespace] || globalTranslations[namespace] || {};
    }
    
    // Se não tem namespace, retorna tudo combinado (pageLang tem prioridade)
    return { ...globalTranslations, ...pageLang };
  };

  const tns = getNamespace();

  return { 
    __, 
    lang: tns, 
    raw: { 
      page: pageLang, 
      global: globalTranslations 
    } 
  };
}