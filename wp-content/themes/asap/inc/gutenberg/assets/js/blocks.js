             /**
             * Lista de íconos (subset ~100)
             */
            var ALL_FA_ICONS = [
                { class: 'fa fa-ad', name: 'ad' },
                { class: 'fa fa-address-book', name: 'address-book' },
                { class: 'fa fa-address-card', name: 'address-card' },
                { class: 'fa fa-adjust', name: 'adjust' },
                { class: 'fa fa-air-freshener', name: 'air-freshener' },
                { class: 'fa fa-align-center', name: 'align-center' },
                { class: 'fa fa-align-justify', name: 'align-justify' },
                { class: 'fa fa-align-left', name: 'align-left' },
                { class: 'fa fa-align-right', name: 'align-right' },
                { class: 'fa fa-ambulance', name: 'ambulance' },
                { class: 'fa fa-anchor', name: 'anchor' },
                { class: 'fa fa-angle-double-down', name: 'angle-double-down' },
                { class: 'fa fa-angle-double-up', name: 'angle-double-up' },
                { class: 'fa fa-angle-down', name: 'angle-down' },
                { class: 'fa fa-angle-up', name: 'angle-up' },
                { class: 'fa fa-apple-alt', name: 'apple-alt' },
                { class: 'fa fa-archive', name: 'archive' },
                { class: 'fa fa-arrow-circle-down', name: 'arrow-circle-down' },
                { class: 'fa fa-arrow-circle-up', name: 'arrow-circle-up' },
                { class: 'fa fa-arrow-down', name: 'arrow-down' },
                { class: 'fa fa-arrow-left', name: 'arrow-left' },
                { class: 'fa fa-arrow-right', name: 'arrow-right' },
                { class: 'fa fa-arrow-up', name: 'arrow-up' },
                { class: 'fa fa-asterisk', name: 'asterisk' },
                { class: 'fa fa-at', name: 'at' },
                { class: 'fa fa-award', name: 'award' },
                { class: 'fa fa-baby', name: 'baby' },
                { class: 'fa fa-balance-scale', name: 'balance-scale' },
                { class: 'fa fa-ban', name: 'ban' },
                { class: 'fa fa-battery-full', name: 'battery-full' },
                { class: 'fa fa-bed', name: 'bed' },
                { class: 'fa fa-bell', name: 'bell' },
                { class: 'fa fa-bicycle', name: 'bicycle' },
                { class: 'fa fa-bomb', name: 'bomb' },
                { class: 'fa fa-book', name: 'book' },
                { class: 'fa fa-bookmark', name: 'bookmark' },
                { class: 'fa fa-briefcase', name: 'briefcase' },
                { class: 'fa fa-bug', name: 'bug' },
                { class: 'fa fa-bullhorn', name: 'bullhorn' },
                { class: 'fa fa-bullseye', name: 'bullseye' },
                { class: 'fa fa-camera', name: 'camera' },
                { class: 'fa fa-car', name: 'car' },
                { class: 'fa fa-cart-plus', name: 'cart-plus' },
                { class: 'fa fa-check', name: 'check' },
                { class: 'fa fa-check-circle', name: 'check-circle' },
                { class: 'fa fa-check-square', name: 'check-square' },
                { class: 'fa fa-chevron-down', name: 'chevron-down' },
                { class: 'fa fa-chevron-left', name: 'chevron-left' },
                { class: 'fa fa-chevron-right', name: 'chevron-right' },
                { class: 'fa fa-chevron-up', name: 'chevron-up' },
                { class: 'fa fa-circle', name: 'circle' },
                { class: 'fa fa-cloud', name: 'cloud' },
                { class: 'fa fa-coffee', name: 'coffee' },
                { class: 'fa fa-cog', name: 'cog' },
                { class: 'fa fa-cogs', name: 'cogs' },
                { class: 'fa fa-comment', name: 'comment' },
                { class: 'fa fa-comments', name: 'comments' },
                { class: 'fa fa-compass', name: 'compass' },
                { class: 'fa fa-copy', name: 'copy' },
                { class: 'fa fa-cut', name: 'cut' },
                { class: 'fa fa-database', name: 'database' },
                { class: 'fa fa-edit', name: 'edit' },
                { class: 'fa fa-eraser', name: 'eraser' },
                { class: 'fa fa-exclamation-circle', name: 'exclamation-circle' },
                { class: 'fa fa-exclamation-triangle', name: 'exclamation-triangle' },
                { class: 'fa fa-eye', name: 'eye' },
                { class: 'fa fa-eye-slash', name: 'eye-slash' },
                { class: 'fa fa-fan', name: 'fan' },
                { class: 'fa fa-fast-forward', name: 'fast-forward' },
                { class: 'fa fa-feather', name: 'feather' },
                { class: 'fa fa-file', name: 'file' },
                { class: 'fa fa-file-alt', name: 'file-alt' },
                { class: 'fa fa-filter', name: 'filter' },
                { class: 'fa fa-fire', name: 'fire' },
                { class: 'fa fa-flag', name: 'flag' },
                { class: 'fa fa-flask', name: 'flask' },
                { class: 'fa fa-folder', name: 'folder' },
                { class: 'fa fa-folder-open', name: 'folder-open' },
                { class: 'fa fa-frown', name: 'frown' },
                { class: 'fa fa-gift', name: 'gift' },
                { class: 'fa fa-globe', name: 'globe' },
                { class: 'fa fa-hdd', name: 'hdd' },
                { class: 'fa fa-heart', name: 'heart' },
                { class: 'fa fa-history', name: 'history' },
                { class: 'fa fa-home', name: 'home' },
                { class: 'fa fa-horse', name: 'horse' },
                { class: 'fa fa-ice-cream', name: 'ice-cream' },
                { class: 'fa fa-image', name: 'image' },
                { class: 'fa fa-info-circle', name: 'info-circle' },
                { class: 'fa fa-key', name: 'key' },
                { class: 'fa fa-lightbulb', name: 'lightbulb' },
                { class: 'fa fa-link', name: 'link' },
                { class: 'fa fa-lock', name: 'lock' },
                { class: 'fa fa-lock-open', name: 'lock-open' },
                { class: 'fa fa-magic', name: 'magic' },
                { class: 'fa fa-map', name: 'map' },
                { class: 'fa fa-map-marker-alt', name: 'map-marker-alt' },
                { class: 'fa fa-medal', name: 'medal' },
                { class: 'fa fa-microphone', name: 'microphone' },
                { class: 'fa fa-mouse', name: 'mouse' },
                { class: 'fa fa-music', name: 'music' },
                { class: 'fa fa-paper-plane', name: 'paper-plane' },
                { class: 'fa fa-paperclip', name: 'paperclip' },
                { class: 'fa fa-pen', name: 'pen' },
                { class: 'fa fa-pen-alt', name: 'pen-alt' },
                { class: 'fa fa-pen-fancy', name: 'pen-fancy' },
                { class: 'fa fa-phone', name: 'phone' },
                { class: 'fa fa-phone-alt', name: 'phone-alt' },
                { class: 'fa fa-plane', name: 'plane' },
                { class: 'fa fa-play', name: 'play' },
                { class: 'fa fa-plus', name: 'plus' },
                { class: 'fa fa-plus-circle', name: 'plus-circle' },
                { class: 'fa fa-print', name: 'print' },
                { class: 'fa fa-question-circle', name: 'question-circle' },
                { class: 'fa fa-quote-left', name: 'quote-left' },
                { class: 'fa fa-quote-right', name: 'quote-right' },
                { class: 'fa fa-recycle', name: 'recycle' },
                { class: 'fa fa-redo', name: 'redo' },
                { class: 'fa fa-reply', name: 'reply' },
                { class: 'fa fa-retweet', name: 'retweet' },
                { class: 'fa fa-road', name: 'road' },
                { class: 'fa fa-robot', name: 'robot' },
                { class: 'fa fa-rocket', name: 'rocket' },
                { class: 'fa fa-rss', name: 'rss' },
                { class: 'fa fa-save', name: 'save' },
                { class: 'fa fa-search', name: 'search' },
                { class: 'fa fa-server', name: 'server' },
                { class: 'fa fa-share', name: 'share' },
                { class: 'fa fa-share-alt', name: 'share-alt' },
                { class: 'fa fa-shield-alt', name: 'shield-alt' },
                { class: 'fa fa-shopping-cart', name: 'shopping-cart' },
                { class: 'fa fa-sign-in-alt', name: 'sign-in-alt' },
                { class: 'fa fa-sign-out-alt', name: 'sign-out-alt' },
                { class: 'fa fa-sitemap', name: 'sitemap' },
                { class: 'fa fa-skull', name: 'skull' },
                { class: 'fa fa-smile', name: 'smile' },
                { class: 'fa fa-smile-beam', name: 'smile-beam' },
                { class: 'fa fa-smile-wink', name: 'smile-wink' },
                { class: 'fa fa-snowflake', name: 'snowflake' },
                { class: 'fa fa-solar-panel', name: 'solar-panel' },
                { class: 'fa fa-star', name: 'star' },
                { class: 'fa fa-star-half-alt', name: 'star-half-alt' },
                { class: 'fa fa-stop', name: 'stop' },
                { class: 'fa fa-stop-circle', name: 'stop-circle' },
                { class: 'fa fa-sync', name: 'sync' },
                { class: 'fa fa-table', name: 'table' },
                { class: 'fa fa-tag', name: 'tag' },
                { class: 'fa fa-tags', name: 'tags' },
                { class: 'fa fa-th-large', name: 'th-large' },
                { class: 'fa fa-thumbs-up', name: 'thumbs-up' },
                { class: 'fa fa-times', name: 'times' },
                { class: 'fa fa-times-circle', name: 'times-circle' },
                { class: 'fa fa-tint', name: 'tint' },
                { class: 'fa fa-toggle-off', name: 'toggle-off' },
                { class: 'fa fa-toggle-on', name: 'toggle-on' },
                { class: 'fa fa-toilet-paper', name: 'toilet-paper' },
                { class: 'fa fa-toolbox', name: 'toolbox' },
                { class: 'fa fa-train', name: 'train' },
                { class: 'fa fa-trash', name: 'trash' },
                { class: 'fa fa-trash-alt', name: 'trash-alt' },
                { class: 'fa fa-tree', name: 'tree' },
                { class: 'fa fa-trophy', name: 'trophy' },
                { class: 'fa fa-truck', name: 'truck' },
                { class: 'fa fa-tv', name: 'tv' },
                { class: 'fa fa-umbrella', name: 'umbrella' },
                { class: 'fa fa-unlock', name: 'unlock' },
                { class: 'fa fa-upload', name: 'upload' },
                { class: 'fa fa-user', name: 'user' },
                { class: 'fa fa-user-circle', name: 'user-circle' },
                { class: 'fa fa-user-secret', name: 'user-secret' },
                { class: 'fa fa-user-shield', name: 'user-shield' },
                { class: 'fa fa-user-tie', name: 'user-tie' },
                { class: 'fa fa-users', name: 'users' },
                { class: 'fa fa-video', name: 'video' },
                { class: 'fa fa-volume-up', name: 'volume-up' },
                { class: 'fa fa-wifi', name: 'wifi' },
                { class: 'fa fa-wrench', name: 'wrench' },
                { class: 'fa fa-yen-sign', name: 'yen-sign' },
                { class: 'fa fa-yin-yang', name: 'yin-yang' },
                { class: 'fa fa-hand-point-up', name: 'hand-point-up' },
                { class: 'fa fa-hand-point-down', name: 'hand-point-down' },
                { class: 'fa fa-thumbs-down', name: 'thumbs-down' }         

            ];
    /**
     * ======================================================
     *  BLOQUE: “FAQ con Schema” (Info Boxes) - Versión Estática
     * ======================================================
     */
    (function (blocks, element, editor, components, i18n) {
      const { __ } = i18n;
      const { registerBlockType } = blocks;
      const { Fragment, memo } = element;
      const { RichText, InspectorControls } = editor;
      const { Button, PanelBody, SelectControl } = components;

      // Helper para crear elementos
      const el = element.createElement;

      // Estilos comunes para FAQItem
      const faqItemContainerStyle = { marginBottom: '20px' };
      const questionStyle = { fontWeight: 'bold', marginBottom: '5px' };

      // Componente para editar un FAQ individual (memoizado para optimizar renderizados)
      const FAQItem = memo((props) => {
        const { index, faq, onChangeFAQ, questionTag } = props;

        const updateQuestion = (value) => {
          onChangeFAQ(index, { ...faq, question: value });
        };

        const updateAnswer = (value) => {
          onChangeFAQ(index, { ...faq, answer: value });
        };

        return el(
          'div',
          { className: 'asap-theme-faq-item', style: faqItemContainerStyle },
          el(RichText, {
            tagName: questionTag,
            value: faq.question,
            onChange: updateQuestion,
            placeholder: __('Pregunta...', 'asap-theme'),
            style: questionStyle
          }),
          el(RichText, {
            tagName: 'p',
            value: faq.answer,
            onChange: updateAnswer,
            placeholder: __('Respuesta...', 'asap-theme')
          })
        );
      });

      // Atributos del bloque
      const blockAttributes = {
        fontSize: {
          type: 'number',
          default: 16
        },
        questionTag: {
          type: 'string',
          default: 'h4'
        },
        faqs: {
          type: 'array',
          default: [
            {
              question: '¿Qué es un nicho de mercado?',
              answer:
                'Un nicho de mercado es un segmento específico del mercado con intereses y necesidades particulares.'
            },
            {
              question: '¿Por qué es importante el SEO?',
              answer:
                'El SEO ayuda a que tu contenido aparezca en los primeros resultados de búsqueda, atrayendo más tráfico y clientes potenciales.'
            }
          ]
        }
      };

      // Registro del bloque FAQ
      registerBlockType('asap-theme/faq-block', {
        title: __('ASAP − FAQ', 'asap-theme'),
        description: __(
          'Crea una sección de preguntas frecuentes (FAQ) estructurada y optimizada para SEO mediante marcado Schema.',
          'asap-theme'
        ),
        icon: 'editor-help',
        category: 'common',
        keywords: ['faq', 'schema', 'asap', 'seo'],
        attributes: blockAttributes,

        edit: (props) => {
          const { attributes, setAttributes } = props;
          const { fontSize, faqs, questionTag } = attributes;

          const onChangeFAQ = (index, newFAQ) => {
            const newFAQs = [...faqs];
            newFAQs[index] = newFAQ;
            setAttributes({ faqs: newFAQs });
          };

          const addNewFAQ = () => {
            setAttributes({
              faqs: [
                ...faqs,
                {
                  question: __('Nueva pregunta', 'asap-theme'),
                  answer: __('Nueva respuesta', 'asap-theme')
                }
              ]
            });
          };

          // Estilos para el contenedor del editor
          const containerStyle = { fontSize: fontSize + 'px'};

          return el(
            Fragment,
            {},
            el(
              InspectorControls,
              {},
              el(
                PanelBody,
                { title: __('Ajustes', 'asap-theme'), initialOpen: true },
                el(SelectControl, {
                  label: __('Etiqueta para la pregunta', 'asap-theme'),
                  value: questionTag,
                  options: [
                    { label: 'H2', value: 'h2' },
                    { label: 'H3', value: 'h3' },
                    { label: 'H4', value: 'h4' },
                    { label: 'Párrafo', value: 'p' }
                  ],
                  onChange: (newTag) => setAttributes({ questionTag: newTag })
                })
              )
            ),
            el(
              'div',
              { className: 'asap-theme-faq-block-editor', style: containerStyle },
              faqs.map((faq, index) =>
                el(FAQItem, {
                  key: index,
                  index: index,
                  faq: faq,
                  onChangeFAQ: onChangeFAQ,
                  questionTag: questionTag
                })
              ),
              el(
                Button,
                {
                  isPrimary: true,
                  onClick: addNewFAQ,
                  style: { marginTop: '10px' }
                },
                __('+ Añadir pregunta', 'asap-theme')
              )
            )
          );
        },

        // Función save que genera el HTML estático
        save: (props) => {
          const { attributes } = props;
          const { fontSize, faqs, questionTag } = attributes;

          const faqItems = faqs.map((faq, index) =>
            el(
              'div',
              { key: index, className: 'asap-theme-faq-item', style: faqItemContainerStyle },
              el(questionTag, { style: questionStyle }, faq.question),
              // Se usa dangerouslySetInnerHTML para interpretar etiquetas HTML (por ejemplo, <br>)
              el('p', { dangerouslySetInnerHTML: { __html: faq.answer } })
            )
          );

          // Marcado Schema en JSON‑LD
          const schemaData = {
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: faqs.map((faq) => ({
              '@type': 'Question',
              name: faq.question,
              acceptedAnswer: {
                '@type': 'Answer',
                text: faq.answer
              }
            }))
          };

          return el(
            'div',
            { className: 'asap-theme-faq-block', style: { fontSize: fontSize + 'px', padding: '20px' } },
            faqItems,
            el('script', { type: 'application/ld+json' }, JSON.stringify(schemaData))
          );
        }
      });
    })(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n);







    /**
     * ======================================================
     *  BLOQUE: “Cajas de Información” (Info Boxes)
     *  con selector visual de íconos (IconPicker).
     * ======================================================
     */
    (function (blocks, element, editor, components, i18n, blockEditor) {
        const { __ } = i18n;
        const { registerBlockType } = blocks;
        const { Fragment, createElement, useState, useEffect, useMemo, memo, useCallback } = element;
        const { InspectorControls, RichText } = editor;
        const { PanelBody, RangeControl, Button, TextControl, SelectControl } = components;
        const { PanelColorSettings } = blockEditor;

        /**
         * ManagedRichText:
         * Componente funcional que actualiza el atributo solo al perder foco.
         */
        const ManagedRichText = (props) => {
            const { value = '', tagName, placeholder, style, formattingControls = [], multiline = false, onChange } = props;
            const [text, setText] = useState(value);
            useEffect(() => { setText(value || ''); }, [value]);
            const handleBlur = useCallback(() => {
                if (onChange) onChange(text);
            }, [onChange, text]);
            return createElement(RichText, {
                tagName,
                value: text,
                placeholder,
                style,
                formattingControls,
                multiline,
                onChange: (val) => setText(val),
                onBlur: handleBlur
            });
        };

        /**
         * removeParagraphTags:
         * Convierte </p> en <br><br> y elimina <p> para evitar romper el layout.
         */
        const removeParagraphTags = (html) => {
            if (!html) return '';
            return html.replace(/<\/p>/gi, '<br><br>').replace(/<p[^>]*>/gi, '');
        };

        /**
         * applyIconSize:
         * Inyecta estilo de tamaño en el primer <span> o <i> del HTML.
         */
        const applyIconSize = (iconHTML, sizePx) => {
            if (!iconHTML) return '';
            const styleStr = `display:inline-block;width:${sizePx}px;height:${sizePx}px;line-height:${sizePx}px;font-size:${sizePx}px;`;
            return iconHTML.replace(/(<(span|i)\b[^>]*)(>)/i, `$1 style="${styleStr}"$3`);
        };

        /**
         * IconPicker:
         * Componente funcional que permite filtrar y seleccionar un ícono.
         * Se asume que ALL_FA_ICONS está definido globalmente.
         */
        const IconPicker = memo((props) => {
            const { onSelect } = props;
            const [filter, setFilter] = useState('');
            const updateFilter = useCallback((val) => setFilter(val.toLowerCase()), []);
            const onSelectIcon = useCallback((iconClass) => { if (onSelect) onSelect(iconClass); }, [onSelect]);
            // Filtrado memorizado de íconos
            const filteredIcons = useMemo(() => {
                return ALL_FA_ICONS.filter(ic => ic.name.toLowerCase().includes(filter));
            }, [filter]);
            // Estilos constantes para el IconPicker
            const containerStyle = { border: '1px solid #ccc', padding: '6px', borderRadius: '4px', marginBottom: '8px' };
            const inputStyle = { marginBottom: '6px' };
            const iconsContainerStyle = { display: 'flex', flexWrap: 'wrap', maxHeight: '160px', overflowY: 'auto' };
            const buttonStyle = {
                width: '60px',
                height: '60px',
                margin: '3px',
                border: '1px solid #ddd',
                borderRadius: '4px',
                background: '#fff',
                cursor: 'pointer',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center'
            };
            const iconStyle = { fontSize: '20px', marginBottom: '4px' };
            const spanStyle = { fontSize: '10px', textAlign: 'center' };

            return createElement(
                'div',
                { style: containerStyle },
                createElement(TextControl, {
                    label: __('Buscar ícono', 'asap-theme'),
                    value: filter,
                    onChange: updateFilter,
                    style: inputStyle
                }),
                createElement(
                    'div',
                    { style: iconsContainerStyle },
                    filteredIcons.map((obj) =>
                        createElement(
                            'button',
                            {
                                key: obj.class,
                                type: 'button',
                                onClick: () => onSelectIcon(obj.class),
                                style: buttonStyle
                            },
                            createElement('i', { className: obj.class, style: iconStyle }),
                            createElement('span', { style: spanStyle }, obj.name)
                        )
                    )
                )
            );
        });

        /**
         * BoxItemEditor:
         * Componente funcional que muestra la caja de información en el editor,
         * con ícono, título, descripción y el selector de íconos.
         * Recibe variables de estilo desde el bloque.
         */
        const BoxItemEditor = memo((props) => {
            const { 
                box, index, onChange, removeBox, boxTitleTag, iconSize, boxBgColor, 
                boxBorderWidth, boxBorderColor, boxBorderRadius, iconColor, titleColor, 
                headingFontSize, textFontSize, textColor,
                contentAlignment, iconAlignment
            } = props;
            const [showIconPicker, setShowIconPicker] = useState(false);
            const toggleIconPicker = useCallback(() => setShowIconPicker(prev => !prev), []);
            const onSelectIcon = useCallback((iconClass) => {
                onChange(index, { icon: `<i class="${iconClass}"></i>` });
                setShowIconPicker(false);
            }, [index, onChange]);
            const clearIcon = useCallback(() => onChange(index, { icon: '' }), [index, onChange]);
            const updateTitle = useCallback((val) => onChange(index, { title: val || '' }), [index, onChange]);
            const updateDesc = useCallback((val) => onChange(index, { description: val || '' }), [index, onChange]);
            const previewIcon = useMemo(() => applyIconSize(box.icon || '', iconSize), [box.icon, iconSize]);

            // Estilos memorizados
            const containerStyle = useMemo(() => ({
                backgroundColor: boxBgColor,
                border: `${boxBorderWidth}px solid ${boxBorderColor}`,
                borderRadius: `${boxBorderRadius}px`,
                padding: '10px',
                marginBottom: '15px'
            }), [boxBgColor, boxBorderWidth, boxBorderColor, boxBorderRadius]);
            const iconContainerStyle = { 
                display: 'flex', 
                justifyContent: iconAlignment, 
                alignItems: 'center', 
                color: iconColor, 
                marginBottom: '10px' 
            };
            const buttonGroupStyle = { 
                display: 'flex', 
                flexDirection: 'column', 
                alignItems: 'center', 
                gap: '10px', 
                marginBottom: '8px' 
            };
            const titleStyle = { 
                color: titleColor, 
                marginBottom: '6px', 
                textAlign: contentAlignment, 
                fontSize: `${headingFontSize}px` 
            };
            const descStyle = { 
                color: textColor, 
                minHeight: '40px', 
                textAlign: contentAlignment, 
                fontSize: `${textFontSize}px` 
            };

            return createElement(
                'div',
                { style: containerStyle },
                createElement('div', {
                    style: iconContainerStyle,
                    dangerouslySetInnerHTML: { __html: previewIcon }
                }),
                createElement(
                    'div',
                    { style: buttonGroupStyle },
                    createElement(Button, { isSecondary: true, onClick: toggleIconPicker },
                        showIconPicker
                            ? __('Cerrar Íconos', 'asap-theme')
                            : (box.icon ? __('Cambiar ícono', 'asap-theme') : __('Elegir Ícono', 'asap-theme'))
                    ),
                    box.icon &&
                        createElement(Button, { isDestructive: true, onClick: clearIcon },
                            __('Quitar ícono', 'asap-theme')
                        )
                ),
                showIconPicker && createElement(IconPicker, { onSelect: onSelectIcon }),
                createElement(ManagedRichText, {
                    tagName: boxTitleTag || 'h3',
                    value: box.title || '',
                    onChange: updateTitle,
                    placeholder: __('Título...', 'asap-theme'),
                    style: titleStyle
                }),
                createElement(ManagedRichText, {
                    tagName: 'div',
                    value: box.description || '',
                    onChange: updateDesc,
                    placeholder: __('Descripción...', 'asap-theme'),
                    style: descStyle
                }),
                createElement(Button, {
                    isDestructive: true,
                    style: { marginTop: '10px', display: 'block', marginLeft: 'auto', marginRight: 'auto' },
                    onClick: () => { if (typeof removeBox === 'function') removeBox(index); }
                }, __('Eliminar caja', 'asap-theme'))
            );
        });

        /**
         * BLOQUE: “Cajas de Información” (Info Boxes)
         */
        registerBlockType('asap-theme/info-box-block', {
            title: __('ASAP − Cajas de información', 'asap-theme'),
            description: __('Crea secciones de información personalizadas con cajas en columnas, donde cada caja incluye un ícono, título y descripción, todo personalizable a tu gusto.', 'asap-theme'),
            icon: 'feedback',
            category: 'common',
            keywords: ['info', 'landing', 'icon', 'caja', 'asap'],

            attributes: {
                mainTitle: { type: 'string', default: '' },
                subTitle: { type: 'string', default: '' },
                columns: { type: 'number', default: 3 },
                boxes: {
                    type: 'array',
                    default: [
                        {
                            icon: '<i class="fa fa-cogs"></i>', 
                            title: __('Intelligent Load', 'asap-theme'),
                            description: __('Only loads CSS/JS when needed, optimizing performance.', 'asap-theme')
                        },
                        {
                            icon: '<i class="fa fa-mobile-alt"></i>', 
                            title: __('Responsive Controls', 'asap-theme'),
                            description: __('Tweak designs for each screen size and preview them in real-time.', 'asap-theme')
                        },
                        {
                            icon: '<i class="fa fa-th-large"></i>', 
                            title: __('Layout Controls', 'asap-theme'),
                            description: __('Build any layout you can imagine with row/layout blocks.', 'asap-theme')
                        }
                    ]
                },
                textColor: { type: 'string', default: '#000000' },
                backgroundColor: { type: 'string', default: '#ffffff' },
                boxBgColor: { type: 'string', default: '#f8f9fa' },
                titleColor: { type: 'string', default: '#2b2b2b' },
                iconColor: { type: 'string', default: '#2B6CB0' },
                iconSize: { type: 'number', default: 40 },
                boxBorderWidth: { type: 'number', default: 1 },
                boxBorderRadius: { type: 'number', default: 8 },
                boxBorderColor: { type: 'string', default: '#ddd' },
                boxTitleTag: { type: 'string', default: 'h3' },
                headingFontSize: { type: 'number', default: 24 },
                textFontSize: { type: 'number', default: 16 },
                contentAlignment: { type: 'string', default: 'center' },
                iconAlignment:    { type: 'string', default: 'center' },
            },

            /* ======================
               EDIT (Editor)
            ====================== */
            edit: (props) => {
                const { attributes, setAttributes } = props;
                const {
                    mainTitle, subTitle,
                    columns, boxes,
                    textColor, backgroundColor, boxBgColor,
                    titleColor, iconColor, iconSize,
                    boxBorderWidth, boxBorderRadius, boxBorderColor,
                    boxTitleTag, headingFontSize, textFontSize,
                    contentAlignment, iconAlignment
                } = attributes;

                if (!attributes.uniqueId) {
                    setAttributes({ uniqueId: 'asap-info-' + Date.now() });
                }

                // Handlers de atributos
                const updateMainTitle = (val) => setAttributes({ mainTitle: val || '' });
                const updateSubTitle = (val) => setAttributes({ subTitle: val || '' });
                const onChangeColumns = (val) => setAttributes({ columns: val });
                const onChangeIconSize = (val) => setAttributes({ iconSize: val });
                const onChangeBoxTitleTag = (val) => setAttributes({ boxTitleTag: val });
                const onChangeBoxBorderWidth = (val) => setAttributes({ boxBorderWidth: val });
                const onChangeBoxBorderRadius = (val) => setAttributes({ boxBorderRadius: val });
                const onChangeHeadingFontSize = (val) => setAttributes({ headingFontSize: val });
                const onChangeTextFontSize = (val) => setAttributes({ textFontSize: val });

                // Funciones para manejar las cajas
                const updateBox = (index, newData) => {
                    const copy = [...boxes];
                    copy[index] = { ...copy[index], ...newData };
                    setAttributes({ boxes: copy });
                };
                const addNewBox = () => {
                    const copy = [...boxes];
                    copy.push({
                        icon: '',
                        title: __('Nuevo título', 'asap-theme'),
                        description: __('Nueva descripción', 'asap-theme')
                    });
                    setAttributes({ boxes: copy });
                };
                const removeBox = (index) => {
                    const copy = [...boxes];
                    copy.splice(index, 1);
                    setAttributes({ boxes: copy });
                };

                // Handlers de colores
                const onChangeTextColor = (val) => setAttributes({ textColor: val });
                const onChangeBgColor = (val) => setAttributes({ backgroundColor: val });
                const onChangeBoxBgColor = (val) => setAttributes({ boxBgColor: val });
                const onChangeTitleColor = (val) => setAttributes({ titleColor: val });
                const onChangeIconColor = (val) => setAttributes({ iconColor: val });
                const onChangeBoxBorderColor = (val) => setAttributes({ boxBorderColor: val });

                // Valores por defecto para resetear
                const defaultAttributes = {
                    mainTitle: '',
                    subTitle: '',
                    columns: 3,
                    boxes: [
                        {
                            icon: '<i class="fa fa-cogs"></i>', 
                            title: __('Intelligent Load', 'asap-theme'),
                            description: __('Only loads CSS/JS when needed, optimizing performance.', 'asap-theme')
                        },
                        {
                            icon: '<i class="fa fa-mobile-alt"></i>', 
                            title: __('Responsive Controls', 'asap-theme'),
                            description: __('Tweak designs for each screen size and preview them in real-time.', 'asap-theme')
                        },
                        {
                            icon: '<i class="fa fa-th-large"></i>', 
                            title: __('Layout Controls', 'asap-theme'),
                            description: __('Build any layout you can imagine with row/layout blocks.', 'asap-theme')
                        }
                    ],
                    textColor: '#000000',
                    backgroundColor: '#ffffff',
                    boxBgColor: '#f8f9fa',
                    titleColor: '#2b2b2b',
                    iconColor: '#2B6CB0',
                    iconSize: 40,
                    boxBorderWidth: 1,
                    boxBorderRadius: 8,
                    boxBorderColor: '#ddd',
                    boxTitleTag: 'h3',
                    headingFontSize: 24,
                    textFontSize: 16,
                    contentAlignment: 'center',
                    iconAlignment: 'center'
                };
                const resetValues = () => setAttributes(defaultAttributes);

                const layoutSettingsPanel = createElement(
                    PanelBody,
                    { title: __('Ajustes generales', 'asap-theme'), initialOpen: true },
                    createElement(RangeControl, {
                        label: __('Columnas', 'asap-theme'),
                        value: columns,
                        onChange: onChangeColumns,
                        min: 1,
                        max: 6
                    }),
                    createElement(RangeControl, {
                        label: __('Tamaño del ícono', 'asap-theme'),
                        value: iconSize,
                        onChange: onChangeIconSize,
                        min: 8,
                        max: 200
                    }),
                    createElement(RangeControl, {
                        label: __('Ancho del borde', 'asap-theme'),
                        value: boxBorderWidth,
                        onChange: onChangeBoxBorderWidth,
                        min: 0,
                        max: 10
                    }),
                    createElement(RangeControl, {
                        label: __('Radio del borde', 'asap-theme'),
                        value: boxBorderRadius,
                        onChange: onChangeBoxBorderRadius,
                        min: 0,
                        max: 999
                    }),
                    createElement(RangeControl, {
                        label: __('Tamaño Encabezados', 'asap-theme'),
                        value: headingFontSize,
                        onChange: onChangeHeadingFontSize,
                        min: 10,
                        max: 100
                    }),
                    createElement(RangeControl, {
                        label: __('Tamaño Texto', 'asap-theme'),
                        value: textFontSize,
                        onChange: onChangeTextFontSize,
                        min: 10,
                        max: 100
                    }),
                    createElement(SelectControl, {
                        label: __('Etiqueta de título de las cajas', 'asap-theme'),
                        value: boxTitleTag,
                        options: [
                            { label: 'H2', value: 'h2' },
                            { label: 'H3', value: 'h3' },
                            { label: 'H4', value: 'h4' },
                            { label: 'H5', value: 'h5' },
                            { label: 'Párrafo', value: 'p' }
                        ],
                        onChange: onChangeBoxTitleTag
                    }),
                    createElement( SelectControl, {
                      label: __('Alineación contenido','asap-theme'),
                      value: contentAlignment,
                      options: [
                        { label: 'Izquierda', value: 'left' },
                        { label: 'Centro',    value: 'center' },
                        { label: 'Derecha',   value: 'right' }
                      ],
                      onChange: val => setAttributes({ contentAlignment: val })
                    } ),

                    createElement( SelectControl, {
                      label: __('Alineación ícono','asap-theme'),
                      value: iconAlignment,
                      options: [
                        { label: 'Izquierda', value: 'flex-start' },
                        { label: 'Centro',    value: 'center'     },
                        { label: 'Derecha',   value: 'flex-end'   }
                      ],
                      onChange: val => setAttributes({ iconAlignment: val })
                    } ),

                );

                const colorSettingsPanel = createElement(
                    PanelColorSettings,
                    {
                        title: __('Colores', 'asap-theme'),
                        initialOpen: true,
                        colorSettings: [
                            { value: textColor, onChange: onChangeTextColor, label: __('Texto', 'asap-theme') },
                            { value: backgroundColor, onChange: onChangeBgColor, label: __('Fondo sección', 'asap-theme') },
                            { value: boxBgColor, onChange: onChangeBoxBgColor, label: __('Fondo cajas', 'asap-theme') },
                            { value: titleColor, onChange: onChangeTitleColor, label: __('Títulos', 'asap-theme') },
                            { value: iconColor, onChange: onChangeIconColor, label: __('Íconos', 'asap-theme') },
                            { value: boxBorderColor, onChange: onChangeBoxBorderColor, label: __('Borde', 'asap-theme') }
                        ]
                    }
                );

                const resetPanel = createElement(
                    PanelBody,
                    { title: __('Restablecer', 'asap-theme'), initialOpen: false },
                    createElement(Button, {
                        onClick: resetValues,
                        isSecondary: true,
                        style: { marginTop: '10px', width: '100%' }
                    }, __('Restablecer valores', 'asap-theme'))
                );

                const inspector = createElement(
                    InspectorControls,
                    {},
                    layoutSettingsPanel,
                    colorSettingsPanel,
                    resetPanel
                );

                const editorContainerStyle = useMemo(() => ({
                    backgroundColor,
                    color: textColor,
                    padding: '20px',
                    borderRadius: '5px'
                }), [backgroundColor, textColor]);

                const gridEditorStyle = useMemo(() => ({
                    display: 'grid',
                    gridTemplateColumns: `repeat(${columns}, minmax(0,1fr))`,
                    gap: '20px'
                }), [columns]);

                const buildBoxEditorList = () => boxes.map((boxItem, idx) =>
                    createElement(BoxItemEditor, {
                        key: idx,
                        index: idx,
                        box: boxItem,
                        onChange: updateBox,
                        removeBox,
                        boxTitleTag,
                        iconSize,
                        boxBgColor,
                        boxBorderWidth,
                        boxBorderColor,
                        boxBorderRadius,
                        iconColor,
                        titleColor,
                        headingFontSize,
                        textFontSize,
                        textColor,
                        contentAlignment,
                        iconAlignment
                    })
                );

                const editorUI = createElement(
                    'div',
                    { className: 'asap-info-boxes-editor', style: editorContainerStyle },
                    createElement('div', { className: 'asap-info-boxes-editor-grid', style: gridEditorStyle }, buildBoxEditorList()),
                    createElement(Button, {
                        isPrimary: true,
                        onClick: addNewBox,
                        style: { marginTop: '20px', display: 'block', marginLeft: 'auto', marginRight: 'auto' }
                    }, __('+ Añadir caja', 'asap-theme'))
                );

                return createElement(Fragment, {}, inspector, editorUI);
            },

            /* ======================
               SAVE (Front)
            ====================== */
            save: (props) => {
                const { attributes } = props;
                const {
                    mainTitle, subTitle, columns, boxes,
                    textColor, backgroundColor, boxBgColor,
                    titleColor, iconColor, iconSize,
                    boxBorderWidth, boxBorderRadius, boxBorderColor,
                    boxTitleTag, headingFontSize, textFontSize,
                    contentAlignment, iconAlignment
                } = attributes;

                const containerGridStyle = {
                    display: 'grid',
                    gridTemplateColumns: `repeat(${columns}, minmax(0,1fr))`,
                    gap: '20px'
                };
                const mediaQuery = '@media(max-width:768px){.asap-info-boxes{grid-template-columns:1fr !important;}}';

                return createElement(
                    Fragment,
                    {},
                    createElement('style', {}, mediaQuery),
                    createElement('div', { className: 'asap-info-section' },
                        createElement('div', { className: 'asap-info-boxes', style: containerGridStyle },
                            boxes.map((box, i) => {
                                const iconHtml = applyIconSize(box.icon || '', iconSize);
                                const descHtml = removeParagraphTags(box.description || '');
                                const TagName = boxTitleTag || 'h3';
                                return createElement(
                                    'div',
                                    {
                                        key: i,
                                        className: 'asap-info-box-item',
                                        style: {
                                            backgroundColor: boxBgColor,
                                            border: `${boxBorderWidth}px solid ${boxBorderColor}`,
                                            borderRadius: `${boxBorderRadius}px`,
                                            padding: '20px',
                                            textAlign: contentAlignment,
                                            boxShadow: '0 1px 2px rgba(0,0,0,0.1)'
                                        }
                                    },
                                    createElement('div', {
                                        className: 'asap-info-box-icon',
                                        style: {
                                            display: 'flex',
                                            justifyContent: iconAlignment,
                                            alignItems: 'center',
                                            color: iconColor,
                                            marginBottom: '10px'
                                        },
                                        dangerouslySetInnerHTML: { __html: iconHtml }
                                    }),
                                    createElement(TagName, {
                                        style: {
                                            color: titleColor,
                                            marginBottom: '10px',
                                            fontSize: `${headingFontSize}px`
                                        }
                                    }, box.title),
                                    createElement('div', {
                                        style: {
                                            margin: 0,
                                            fontSize: `${textFontSize}px`,
                                            lineHeight: '1.5',
                                            color: textColor,
                                            whiteSpace: 'pre-line'
                                        },
                                        dangerouslySetInnerHTML: { __html: descHtml }
                                    })
                                );
                            })
                        )
                    )
                );
            }
        });
    })( 
        window.wp.blocks,
        window.wp.element,
        window.wp.editor,
        window.wp.components,
        window.wp.i18n,
        window.wp.blockEditor
    );








    // ------------------------------------------------------------
    // Bloque: ASAP − Tabla comparativa / Pros & Contras
    // ------------------------------------------------------------
    ( function ( blocks, element, editor, components, i18n, blockEditor ) {
        const { __ } = i18n;
        const { registerBlockType } = blocks;
        const { Fragment, createElement, useState, useEffect, useMemo, useCallback, memo } = element;
        const { InspectorControls, RichText } = editor;
        const { Button, TextControl, SelectControl, RangeControl, PanelBody } = components;
        const { PanelColorSettings } = blockEditor;

        // ------------------------------------------------------------
        // ManagedRichText: actualiza el atributo solo al perder foco
        // ------------------------------------------------------------
        const ManagedRichText = ( props ) => {
            const { value = '', tagName, placeholder, style, formattingControls = [], multiline = false, onChange } = props;
            const [ text, setText ] = useState( value );
            useEffect( () => {
                setText( value || '' );
            }, [ value ] );
            const handleBlur = useCallback( () => {
                if ( onChange ) {
                    onChange( text );
                }
            }, [ onChange, text ] );
            return createElement( RichText, {
                tagName,
                value: text,
                placeholder,
                style,
                formattingControls,
                multiline,
                onChange: ( val ) => setText( val ),
                onBlur: handleBlur
            } );
        };

        // ------------------------------------------------------------
        // removeParagraphTags: convierte </p> en <br><br> y elimina <p>
        // ------------------------------------------------------------
        const removeParagraphTags = ( html ) => {
            if ( ! html ) return '';
            return html.replace( /<\/p>/gi, '<br><br>' ).replace( /<p[^>]*>/gi, '' );
        };

        // ------------------------------------------------------------
        // applyIconSize: inyecta estilo de tamaño en el primer <span> o <i>
        // ------------------------------------------------------------
        const applyIconSize = ( iconHTML, sizePx ) => {
            if ( ! iconHTML ) return '';
            const styleStr = `display:inline-block;width:${ sizePx }px;height:${ sizePx }px;line-height:${ sizePx }px;font-size:${ sizePx }px;`;
            return iconHTML.replace( /(<(span|i)\b[^>]*)(>)/i, `$1 style="${ styleStr }"$3` );
        };

        // ------------------------------------------------------------
        // IconPicker: selector de íconos (se asume ALL_FA_ICONS global)
        // ------------------------------------------------------------
        const IconPicker = memo( ( props ) => {
            const { onSelect } = props;
            const [ filter, setFilter ] = useState( '' );
            const updateFilter = useCallback( ( val ) => {
                setFilter( val.toLowerCase() );
            }, [] );
            const onSelectIcon = useCallback( ( iconClass ) => {
                if ( onSelect ) onSelect( iconClass );
            }, [ onSelect ] );
            const filtered = useMemo( () => {
                return ALL_FA_ICONS.filter( ic => ic.name.toLowerCase().includes( filter ) );
            }, [ filter ] );
            return createElement(
                'div',
                {
                    style: {
                        border: '1px solid #ccc',
                        padding: '6px',
                        borderRadius: '4px',
                        marginBottom: '1em',
                        maxWidth: '250px'
                    }
                },
                createElement( TextControl, {
                    label: __( 'Buscar ícono:', 'asap-theme' ),
                    value: filter,
                    onChange: updateFilter,
                    style: { marginBottom: '6px' }
                } ),
                createElement(
                    'div',
                    {
                        style: {
                            display: 'flex',
                            flexWrap: 'wrap',
                            maxHeight: '160px',
                            overflowY: 'auto'
                        }
                    },
                    filtered.map( obj =>
                        createElement(
                            'button',
                            {
                                key: obj.class,
                                type: 'button',
                                onClick: () => onSelectIcon( obj.class ),
                                style: {
                                    width: '60px',
                                    height: '60px',
                                    margin: '3px',
                                    border: '1px solid #ddd',
                                    borderRadius: '4px',
                                    background: '#fff',
                                    cursor: 'pointer',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                    justifyContent: 'center'
                                }
                            },
                            createElement( 'i', {
                                className: obj.class,
                                style: { fontSize: '20px', marginBottom: '4px' }
                            } ),
                            createElement( 'span', {
                                style: { fontSize: '10px', textAlign: 'center' }
                            }, obj.name )
                        )
                    )
                )
            );
        } );

        // ------------------------------------------------------------
        // ItemEditor: componente para editar cada ítem (en columnas izquierda y derecha)
        // ------------------------------------------------------------
        const ItemEditor = memo( ( props ) => {
            const { item, index, onUpdate, onRemove, iconColor } = props;
            const [ showPicker, setShowPicker ] = useState( false );
            const togglePicker = useCallback( () => {
                setShowPicker( prev => ! prev );
            }, [] );
            const onSelectIcon = useCallback( ( iconClass ) => {
                const newItem = { icon: `<i class="${ iconClass }"></i>`, text: item.text || '' };
                onUpdate( index, newItem );
                setShowPicker( false );
            }, [ index, item, onUpdate ] );
            const clearIcon = useCallback( () => {
                const newItem = { icon: '', text: item.text || '' };
                onUpdate( index, newItem );
            }, [ index, item, onUpdate ] );
            const updateText = useCallback( ( val ) => {
                const newItem = { icon: item.icon || '', text: val || '' };
                onUpdate( index, newItem );
            }, [ index, item, onUpdate ] );
            return createElement(
                'div',
                { style: { marginBottom: '10px' } },
                createElement(
                    'div',
                    { style: { display: 'flex', alignItems: 'center', marginBottom: '8px' } },
                    item.icon && item.icon.trim()
                        ? createElement( 'div', {
                              style: { fontSize: '20px', color: iconColor, marginRight: '10px' },
                              dangerouslySetInnerHTML: { __html: item.icon }
                          } )
                        : null,
                    createElement( ManagedRichText, {
                        tagName: 'div',
                        value: item.text || '',
                        onChange: updateText,
                        style: { minHeight: '30px', flex: '1' }
                    } )
                ),
                createElement(
                    'div',
                    { style: { display: 'flex', gap: '10px' } },
                    createElement(
                        Button,
                        { isSecondary: true, onClick: togglePicker },
                        showPicker
                            ? __( 'Cerrar Íconos', 'asap-theme' )
                            : ( item.icon ? __( 'Cambiar ícono', 'asap-theme' ) : __( 'Elegir Ícono', 'asap-theme' ) )
                    ),
                    item.icon &&
                        createElement( Button, { isDestructive: true, onClick: clearIcon },
                            __( 'Quitar ícono', 'asap-theme' )
                        ),
                    createElement(
                        Button,
                        { isDestructive: true, onClick: () => { if ( typeof onRemove === 'function' ) onRemove( index ); } },
                        __( 'Eliminar ítem', 'asap-theme' )
                    )
                ),
                showPicker && createElement( IconPicker, { onSelect: onSelectIcon } )
            );
        } );

        // ------------------------------------------------------------
        // LeftColumnEditor: edición de la columna izquierda
        // ------------------------------------------------------------
        const LeftColumnEditor = ( props ) => {
            const { leftTitle, onChangeLeftTitle, leftItems, updateLeftItem, removeLeftItem, titleTag, titleColor, titleAlignment, leftIconColor, designStyle, design1ShadowColor, design2BorderWidth, design2BorderColor, design2BorderRadius, setLeftItems } = props;
            const columnStyle = useMemo( () => {
                let st = { flex: '0 0 50%', maxWidth: '50%', boxSizing: 'border-box', overflowWrap: 'break-word', marginRight: '10px' };
                if ( designStyle === '2' ) {
                    st = { ...st, border: `${ design2BorderWidth }px solid ${ design2BorderColor }`, borderRadius: `${ design2BorderRadius }px`, padding: '10px' };
                }
                return st;
            }, [ designStyle, design2BorderWidth, design2BorderColor, design2BorderRadius ] );
            return createElement(
                'div',
                { style: columnStyle },
                createElement( ManagedRichText, {
                    tagName: titleTag,
                    value: leftTitle,
                    onChange: onChangeLeftTitle,
                    style: { color: titleColor, marginBottom: '15px', marginTop: '0', textAlign: titleAlignment }
                } ),
                leftItems.map( ( item, index ) =>
                    createElement( ItemEditor, {
                        key: index,
                        item,
                        index,
                        onUpdate: updateLeftItem,
                        onRemove: removeLeftItem,
                        iconColor: leftIconColor
                    } )
                ),
                createElement(
                    Button,
                    { isPrimary: true, style: { marginTop: '10px' }, onClick: () => {
                        const copy = [ ...leftItems ];
                        copy.push( { icon: '', text: __( 'Nuevo ítem...', 'asap-theme' ) } );
                        setLeftItems( copy );
                    } },
                    __( '+ Añadir ítem', 'asap-theme' )
                )
            );
        };

        // ------------------------------------------------------------
        // RightColumnEditor: edición de la columna derecha
        // ------------------------------------------------------------
        const RightColumnEditor = ( props ) => {
            const { rightTitle, onChangeRightTitle, rightItems, updateRightItem, removeRightItem, titleTag, titleColor, titleAlignment, rightIconColor, designStyle, design1ShadowColor, design2BorderWidth, design2BorderColor, design2BorderRadius, setRightItems } = props;
            const columnStyle = useMemo( () => {
                let st = { flex: '0 0 50%', maxWidth: '50%', boxSizing: 'border-box', overflowWrap: 'break-word', marginLeft: '10px' };
                if ( designStyle === '2' ) {
                    st = { ...st, border: `${ design2BorderWidth }px solid ${ design2BorderColor }`, borderRadius: `${ design2BorderRadius }px`, padding: '10px' };
                }
                return st;
            }, [ designStyle, design2BorderWidth, design2BorderColor, design2BorderRadius ] );
            return createElement(
                'div',
                { style: columnStyle },
                createElement( ManagedRichText, {
                    tagName: titleTag,
                    value: rightTitle,
                    onChange: onChangeRightTitle,
                    style: { color: titleColor, marginBottom: '15px', marginTop: '0', textAlign: titleAlignment }
                } ),
                rightItems.map( ( item, index ) =>
                    createElement( ItemEditor, {
                        key: index,
                        item,
                        index,
                        onUpdate: updateRightItem,
                        onRemove: removeRightItem,
                        iconColor: rightIconColor
                    } )
                ),
                createElement(
                    Button,
                    { isPrimary: true, style: { marginTop: '10px' }, onClick: () => {
                        const copy = [ ...rightItems ];
                        copy.push( { icon: '', text: __( 'Nuevo ítem...', 'asap-theme' ) } );
                        setRightItems( copy );
                    } },
                    __( '+ Añadir ítem', 'asap-theme' )
                )
            );
        };

        // ------------------------------------------------------------
        // Registro del bloque
        // ------------------------------------------------------------
        registerBlockType( 'asap-theme/procons-block', {
            title: __( 'ASAP − Tabla comparativa', 'asap-theme' ),
            description: __( 'Compara diferentes opciones con una tabla comparativa. Cada ítem combina un ícono y un texto descriptivo, y puedes elegir entre 3 diseños distintos para adaptarlo a tu estilo.', 'asap-theme' ),
            icon: 'columns',
            category: 'common',
            attributes: {
                leftTitle: { type: 'string', default: __( 'Título izquierda', 'asap-theme' ) },
                rightTitle: { type: 'string', default: __( 'Título derecha', 'asap-theme' ) },
                titleTag: { type: 'string', default: 'h3' },
                titleAlignment: { type: 'string', default: 'center' },
                leftItems: {
                    type: 'array',
                    default: [
                        { icon: '<i class="fa fa-thumbs-up"></i>', text: __( 'Es muy rápido', 'asap-theme' ) },
                        { icon: '<i class="fa fa-thumbs-up"></i>', text: __( 'Soporte excelente', 'asap-theme' ) }
                    ]
                },
                rightItems: {
                    type: 'array',
                    default: [
                        { icon: '<i class="fa fa-thumbs-down"></i>', text: __( 'Precio elevado', 'asap-theme' ) }
                    ]
                },
                bgColor: { type: 'string', default: '#ffffff' },
                textColor: { type: 'string', default: '#000000' },
                titleColor: { type: 'string', default: '#2b2b2b' },
                leftIconColor: { type: 'string', default: '#2B6CB0' },
                rightIconColor: { type: 'string', default: '#E53E3E' },
                designStyle: { type: 'string', default: '1' },
                design1ShadowColor: { type: 'string', default: 'rgba(0,0,0,0.2)' },
                design2BorderColor: { type: 'string', default: '#ccc' },
                design2BorderWidth: { type: 'number', default: 1 },
                design2BorderRadius: { type: 'number', default: 4 },
                design3Color1: { type: 'string', default: '#4FD1C5' },
                design3Color2: { type: 'string', default: '#63B3ED' },
                design3Angle: { type: 'number', default: 45 },
                design3ItemRotation: { type: 'number', default: 0 }
            },
            edit: ( props ) => {
                const { attributes, setAttributes } = props;
                const {
                    leftTitle, rightTitle, titleTag, titleAlignment,
                    leftItems, rightItems,
                    bgColor, textColor, titleColor,
                    leftIconColor, rightIconColor,
                    designStyle,
                    design1ShadowColor,
                    design2BorderColor, design2BorderWidth, design2BorderRadius,
                    design3Color1, design3Color2, design3Angle, design3ItemRotation
                } = attributes;

                // Handlers para títulos
                const onChangeLeftTitle = useCallback( ( v ) => setAttributes({ leftTitle: v || '' }), [ setAttributes ] );
                const onChangeRightTitle = useCallback( ( v ) => setAttributes({ rightTitle: v || '' }), [ setAttributes ] );
                const onChangeTitleTag = useCallback( ( v ) => setAttributes({ titleTag: v }), [ setAttributes ] );
                const onChangeTitleAlignment = useCallback( ( v ) => setAttributes({ titleAlignment: v }), [ setAttributes ] );

                // Handlers para ítems de la columna izquierda
                const updateLeftItem = useCallback( ( index, newItem ) => {
                    const copy = [ ...leftItems ];
                    copy[ index ] = newItem;
                    setAttributes({ leftItems: copy });
                }, [ leftItems, setAttributes ] );
                const addLeftItem = useCallback( () => {
                    const copy = [ ...leftItems ];
                    copy.push({ icon: '', text: __( 'Nuevo ítem...', 'asap-theme' ) });
                    setAttributes({ leftItems: copy });
                }, [ leftItems, setAttributes ] );
                const removeLeftItem = useCallback( ( index ) => {
                    const copy = [ ...leftItems ];
                    copy.splice( index, 1 );
                    setAttributes({ leftItems: copy });
                }, [ leftItems, setAttributes ] );

                // Handlers para ítems de la columna derecha
                const updateRightItem = useCallback( ( index, newItem ) => {
                    const copy = [ ...rightItems ];
                    copy[ index ] = newItem;
                    setAttributes({ rightItems: copy });
                }, [ rightItems, setAttributes ] );
                const addRightItem = useCallback( () => {
                    const copy = [ ...rightItems ];
                    copy.push({ icon: '', text: __( 'Nuevo ítem...', 'asap-theme' ) });
                    setAttributes({ rightItems: copy });
                }, [ rightItems, setAttributes ] );
                const removeRightItem = useCallback( ( index ) => {
                    const copy = [ ...rightItems ];
                    copy.splice( index, 1 );
                    setAttributes({ rightItems: copy });
                }, [ rightItems, setAttributes ] );

                // Handlers para colores
                const onChangeBgColor = useCallback( ( v ) => setAttributes({ bgColor: v }), [ setAttributes ] );
                const onChangeTextColor = useCallback( ( v ) => setAttributes({ textColor: v }), [ setAttributes ] );
                const onChangeTitleColor = useCallback( ( v ) => setAttributes({ titleColor: v }), [ setAttributes ] );
                const onChangeLeftIconColor = useCallback( ( v ) => setAttributes({ leftIconColor: v }), [ setAttributes ] );
                const onChangeRightIconColor = useCallback( ( v ) => setAttributes({ rightIconColor: v }), [ setAttributes ] );

                // Handlers para diseño
                const onChangeDesignStyle = useCallback( ( v ) => setAttributes({ designStyle: v }), [ setAttributes ] );
                const onChangeDesign1ShadowColor = useCallback( ( v ) => setAttributes({ design1ShadowColor: v }), [ setAttributes ] );
                const onChangeDesign2BorderColor = useCallback( ( v ) => setAttributes({ design2BorderColor: v }), [ setAttributes ] );
                const onChangeDesign2BorderWidth = useCallback( ( v ) => setAttributes({ design2BorderWidth: v || 0 }), [ setAttributes ] );
                const onChangeDesign2BorderRadius = useCallback( ( v ) => setAttributes({ design2BorderRadius: v || 0 }), [ setAttributes ] );
                const onChangeDesign3Color1 = useCallback( ( v ) => setAttributes({ design3Color1: v }), [ setAttributes ] );
                const onChangeDesign3Color2 = useCallback( ( v ) => setAttributes({ design3Color2: v }), [ setAttributes ] );
                const onChangeDesign3Angle = useCallback( ( v ) => setAttributes({ design3Angle: v || 0 }), [ setAttributes ] );
                const onChangeDesign3ItemRotation = useCallback( ( v ) => setAttributes({ design3ItemRotation: v || 0 }), [ setAttributes ] );

                // Función para generar estilos de la sección en el editor
                const getSectionStyleEditor = useCallback( () => {
                    let style = {
                        backgroundColor: bgColor,
                        color: textColor,
                        padding: '20px',
                        borderRadius: '5px'
                    };
                    if ( designStyle === '3' ) {
                        const gradient = `linear-gradient(${ design3Angle }deg, ${ design3Color1 }, ${ design3Color2 })`;
                        style = { ...style, background: gradient, padding: '20px' };
                    }
                    return style;
                }, [ bgColor, textColor, designStyle, design3Angle, design3Color1, design3Color2 ] );

                // Panel de diseño y colores, incluyendo el reset, se muestra en el Inspector
                const resetPanel = createElement(
                    PanelBody,
                    { title: __( 'Restablecer', 'asap-theme' ), initialOpen: false },
                    createElement( Button, {
                        onClick: () => setAttributes({
                            leftTitle: __( 'Título izquierda', 'asap-theme' ),
                            rightTitle: __( 'Título derecha', 'asap-theme' ),
                            titleTag: 'h3',
                            titleAlignment: 'center',
                            leftItems: [
                                { icon: '<i class="fa fa-thumbs-up"></i>', text: __( 'Es muy rápido', 'asap-theme' ) },
                                { icon: '<i class="fa fa-thumbs-up"></i>', text: __( 'Soporte excelente', 'asap-theme' ) }
                            ],
                            rightItems: [
                                { icon: '<i class="fa fa-thumbs-down"></i>', text: __( 'Precio elevado', 'asap-theme' ) }
                            ],
                            bgColor: '#ffffff',
                            textColor: '#000000',
                            titleColor: '#2b2b2b',
                            leftIconColor: '#2B6CB0',
                            rightIconColor: '#E53E3E',
                            designStyle: '1',
                            design1ShadowColor: 'rgba(0,0,0,0.2)',
                            design2BorderColor: '#ccc',
                            design2BorderWidth: 1,
                            design2BorderRadius: 4,
                            design3Color1: '#4FD1C5',
                            design3Color2: '#63B3ED',
                            design3Angle: 45,
                            design3ItemRotation: 0
                        }),
                        isSecondary: true,
                        style: { marginTop: '10px', width: '100%' }
                    }, __( 'Restablecer valores', 'asap-theme' ) )
                );

                const inspector = createElement( InspectorControls, {}, 
                    createElement(
                        PanelBody,
                        { title: __( 'Opciones de diseño', 'asap-theme' ), initialOpen: true },
                        createElement( SelectControl, {
                            label: __( 'Diseño', 'asap-theme' ),
                            value: designStyle,
                            options: [
                                { label: 'Diseño 1', value: '1' },
                                { label: 'Diseño 2', value: '2' },
                                { label: 'Diseño 3', value: '3' }
                            ],
                            onChange: onChangeDesignStyle
                        } ),
                        createElement( SelectControl, {
                            label: __( 'Etiqueta para títulos', 'asap-theme' ),
                            value: titleTag,
                            options: [
                                { label: 'H2', value: 'h2' },
                                { label: 'H3', value: 'h3' },
                                { label: 'H4', value: 'h4' },
                                { label: 'p', value: 'p' }
                            ],
                            onChange: onChangeTitleTag
                        } ),
                        createElement( SelectControl, {
                            label: __( 'Alineación de Títulos', 'asap-theme' ),
                            value: titleAlignment,
                            options: [
                                { label: 'Izquierda', value: 'left' },
                                { label: 'Centro', value: 'center' },
                                { label: 'Derecha', value: 'right' }
                            ],
                            onChange: onChangeTitleAlignment
                        } ),
                        designStyle === '1' &&
                            createElement(
                                'div',
                                {},
                                createElement( 'p', {}, __( 'Ajustes Diseño 1:', 'asap-theme' ) ),
                                createElement( PanelColorSettings, {
                                    title: __( 'Color sombra', 'asap-theme' ),
                                    initialOpen: true,
                                    colorSettings: [
                                        {
                                            value: design1ShadowColor,
                                            onChange: onChangeDesign1ShadowColor,
                                            label: __( 'Sombra en items', 'asap-theme' )
                                        }
                                    ]
                                } )
                            ),
                        designStyle === '2' &&
                            createElement(
                                'div',
                                {},
                                createElement( 'p', {}, __( 'Ajustes Diseño 2:', 'asap-theme' ) ),
                                createElement( PanelColorSettings, {
                                    title: __( 'Borde (en columna)', 'asap-theme' ),
                                    initialOpen: true,
                                    colorSettings: [
                                        {
                                            value: design2BorderColor,
                                            onChange: onChangeDesign2BorderColor,
                                            label: __( 'Color del borde', 'asap-theme' )
                                        }
                                    ]
                                } ),
                                createElement( RangeControl, {
                                    label: __( 'Grosor del borde (px)', 'asap-theme' ),
                                    value: design2BorderWidth,
                                    onChange: onChangeDesign2BorderWidth,
                                    min: 0,
                                    max: 20
                                } ),
                                createElement( RangeControl, {
                                    label: __( 'Radio del borde (px)', 'asap-theme' ),
                                    value: design2BorderRadius,
                                    onChange: onChangeDesign2BorderRadius,
                                    min: 0,
                                    max: 50
                                } )
                            ),
                        designStyle === '3' &&
                            createElement(
                                'div',
                                {},
                                createElement( 'p', {}, __( 'Ajustes Diseño 3:', 'asap-theme' ) ),
                                createElement( PanelColorSettings, {
                                    title: __( 'Gradiente', 'asap-theme' ),
                                    initialOpen: true,
                                    colorSettings: [
                                        {
                                            value: design3Color1,
                                            onChange: onChangeDesign3Color1,
                                            label: __( 'Color 1', 'asap-theme' )
                                        },
                                        {
                                            value: design3Color2,
                                            onChange: onChangeDesign3Color2,
                                            label: __( 'Color 2', 'asap-theme' )
                                        }
                                    ]
                                } ),
                                createElement( RangeControl, {
                                    label: __( 'Ángulo del gradiente (0 - 360)', 'asap-theme' ),
                                    value: design3Angle,
                                    onChange: onChangeDesign3Angle,
                                    min: 0,
                                    max: 360
                                } ),
                                createElement( RangeControl, {
                                    label: __( 'Rotación de ítems (°)', 'asap-theme' ),
                                    value: design3ItemRotation,
                                    onChange: onChangeDesign3ItemRotation,
                                    min: -20,
                                    max: 20
                                } )
                            ),
                        // Se incluye el panel de "Restablecer" dentro del Inspector
                        resetPanel
                    ),
                    createElement(
                        PanelColorSettings,
                        {
                            title: __( 'Colores', 'asap-theme' ),
                            initialOpen: false,
                            colorSettings: [
                                { value: bgColor, onChange: onChangeBgColor, label: __( 'Fondo sección', 'asap-theme' ) },
                                { value: textColor, onChange: onChangeTextColor, label: __( 'Texto', 'asap-theme' ) },
                                { value: titleColor, onChange: onChangeTitleColor, label: __( 'Títulos', 'asap-theme' ) },
                                { value: leftIconColor, onChange: onChangeLeftIconColor, label: __( 'Iconos columna izquierda', 'asap-theme' ) },
                                { value: rightIconColor, onChange: onChangeRightIconColor, label: __( 'Iconos columna derecha', 'asap-theme' ) }
                            ]
                        }
                    )
                );

                const sectionStyle = useMemo( () => getSectionStyleEditor(), [ getSectionStyleEditor ] );
                const columnsContainerStyle = { display: 'flex', justifyContent: 'space-between' };

                const editorUI = createElement(
                    'div',
                    { className: 'asap-procons-section', style: sectionStyle },
                    createElement(
                        'div',
                        { className: 'asap-procons-columns', style: columnsContainerStyle },
                        createElement( LeftColumnEditor, {
                            leftTitle,
                            onChangeLeftTitle,
                            leftItems,
                            updateLeftItem,
                            removeLeftItem,
                            titleTag,
                            titleColor,
                            titleAlignment,
                            leftIconColor,
                            designStyle,
                            design1ShadowColor,
                            design2BorderWidth,
                            design2BorderColor,
                            design2BorderRadius,
                            setLeftItems: ( val ) => setAttributes({ leftItems: val })
                        } ),
                        createElement( RightColumnEditor, {
                            rightTitle,
                            onChangeRightTitle,
                            rightItems,
                            updateRightItem,
                            removeRightItem,
                            titleTag,
                            titleColor,
                            titleAlignment,
                            rightIconColor,
                            designStyle,
                            design1ShadowColor,
                            design2BorderWidth,
                            design2BorderColor,
                            design2BorderRadius,
                            setRightItems: ( val ) => setAttributes({ rightItems: val })
                        } )
                    )
                );

                return createElement( Fragment, {}, inspector, editorUI );
            },
            save: ( props ) => {
                const { attributes } = props;
                const {
                    leftTitle, rightTitle, titleTag, titleAlignment,
                    leftItems, rightItems,
                    bgColor, textColor, titleColor,
                    leftIconColor, rightIconColor,
                    designStyle,
                    design1ShadowColor,
                    design2BorderColor, design2BorderWidth, design2BorderRadius,
                    design3Color1, design3Color2, design3Angle, design3ItemRotation
                } = attributes;

                const mediaQuery = '@media(max-width:768px){.asap-procons-columns{flex-direction:column !important;}}';
                let sectionStyle = {
                    backgroundColor: bgColor,
                    color: textColor
                };
                if ( designStyle === '3' ) {
                    const grad = `linear-gradient(${ design3Angle }deg, ${ design3Color1 }, ${ design3Color2 })`;
                    sectionStyle = { ...sectionStyle, background: grad, padding: '24px 20px', borderRadius: '6px' };
                }
                const getColumnStyle = ( side ) => {
                    let st = { flex: '1 1 0%' };
                    st = side === 'left' ? { ...st, marginRight: '10px' } : { ...st, marginLeft: '10px' };
                    if ( designStyle === '2' ) {
                        st = { ...st, border: `${ design2BorderWidth }px solid ${ design2BorderColor }`, borderRadius: `${ design2BorderRadius }px`, padding: '20px 10px' };
                    }
                    return st;
                };
                const getItemStyle = () => {
                    if ( designStyle === '1' ) {
                        return {
                            border: '1px solid #eee',
                            borderRadius: '4px',
                            padding: '10px',
                            marginBottom: '14px',
                            boxShadow: `0 2px 5px ${ design1ShadowColor }`
                        };
                    } else if ( designStyle === '2' ) {
                        return { padding: '10px', marginBottom: '10px' };
                    } else if ( designStyle === '3' ) {
                        return {
                            padding: '10px',
                            marginBottom: '10px',
                            transform: `rotate(${ design3ItemRotation }deg)`,
                            background: 'rgba(255,255,255,0.8)',
                            borderRadius: '6px'
                        };
                    }
                    return { padding: '10px', marginBottom: '10px' };
                };
                const leftColumnStyle = getColumnStyle( 'left' );
                const rightColumnStyle = getColumnStyle( 'right' );
                const itemStyle = getItemStyle();
                return createElement(
                    Fragment,
                    {},
                    createElement( 'style', {}, mediaQuery ),
                    createElement(
                        'div',
                        { className: 'asap-procons-section', style: sectionStyle },
                        createElement(
                            'div',
                            { className: 'asap-procons-columns', style: { display: 'flex', justifyContent: 'space-between' } },
                            // Columna izquierda
                            createElement(
                                'div',
                                { className: 'asap-procons-left', style: leftColumnStyle },
                                createElement( titleTag, {
                                    style: { color: titleColor, marginBottom: '15px !important', textAlign: titleAlignment },
                                    dangerouslySetInnerHTML: { __html: removeParagraphTags( leftTitle ) }
                                } ),
                                leftItems.map( ( it, i ) => {
                                    const textClean = removeParagraphTags( it.text || '' );
                                    return createElement(
                                        'div',
                                        { key: i, style: itemStyle },
                                        createElement(
                                            'div',
                                            { style: { display: 'flex', alignItems: 'center' } },
                                            it.icon &&
                                                createElement( 'div', {
                                                    style: { fontSize: '20px', color: leftIconColor, marginRight: '10px' },
                                                    dangerouslySetInnerHTML: { __html: it.icon }
                                                } ),
                                            createElement( 'div', { style: { whiteSpace: 'pre-line' }, dangerouslySetInnerHTML: { __html: textClean } } )
                                        )
                                    );
                                } )
                            ),
                            // Columna derecha
                            createElement(
                                'div',
                                { className: 'asap-procons-right', style: rightColumnStyle },
                                createElement( titleTag, {
                                    style: { color: titleColor, marginBottom: '15px !important', textAlign: titleAlignment },
                                    dangerouslySetInnerHTML: { __html: removeParagraphTags( rightTitle ) }
                                } ),
                                rightItems.map( ( it, i ) => {
                                    const textClean = removeParagraphTags( it.text || '' );
                                    return createElement(
                                        'div',
                                        { key: i, style: itemStyle },
                                        createElement(
                                            'div',
                                            { style: { display: 'flex', alignItems: 'center' } },
                                            it.icon &&
                                                createElement( 'div', {
                                                    style: { fontSize: '20px', color: rightIconColor, marginRight: '10px' },
                                                    dangerouslySetInnerHTML: { __html: it.icon }
                                                } ),
                                            createElement( 'div', { style: { whiteSpace: 'pre-line' }, dangerouslySetInnerHTML: { __html: textClean } } )
                                        )
                                    );
                                } )
                            )
                        )
                    )
                );
            }
        } );
    } )( window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor );




    (function (blocks, element, editor, components, i18n, blockEditor) {
      const { __ } = i18n;
      const { registerBlockType } = blocks;
      const { Fragment, createElement, useState, useEffect, useMemo } = element;
      const { InspectorControls, RichText } = editor;
      const { PanelBody, Button, SelectControl, TextControl, RangeControl, ToggleControl } = components;
      const { PanelColorSettings } = blockEditor;

      // Función externa para obtener colores por defecto según el tipo
      const getDefaultColors = (type) => {
        switch (type) {
          case 'info':
            return { bg: '#d9edf7', border: '#bce8f1', text: '#31708f', icon: 'fa fa-info-circle' };
          case 'warning':
            return { bg: '#fcf8e3', border: '#faebcc', text: '#8a6d3b', icon: 'fa fa-exclamation-triangle' };
          case 'success':
            return { bg: '#dff0d8', border: '#d0e9c6', text: '#3c763d', icon: 'fa fa-check-circle' };
          case 'danger':
            return { bg: '#f2dede', border: '#ebcccc', text: '#a94442', icon: 'fa fa-times-circle' };
          default:
            return { bg: '#f0f0f0', border: '#ccc', text: '#333', icon: '' };
        }
      };

      /**
       * ManagedRichText
       * Componente funcional que evita “lag” al escribir, actualizando el atributo en onBlur.
       */
      const ManagedRichText = (props) => {
        const { value = '', tagName, placeholder, style, formattingControls = [], multiline = false, onChange } = props;
        const [text, setText] = useState(value);
        useEffect(() => {
          setText(value || '');
        }, [value]);
        return createElement(RichText, {
          tagName,
          value: text,
          placeholder,
          style,
          formattingControls,
          multiline,
          onChange: (val) => setText(val),
          onBlur: () => { if (onChange) onChange(text); }
        });
      };

      /**
       * removeParagraphTags()
       * Reemplaza </p> por <br><br> y elimina <p> para evitar romper el layout.
       */
      const removeParagraphTags = (html) =>
        html ? html.replace(/<\/p>/gi, '<br><br>').replace(/<p[^>]*>/gi, '') : '';

      /**
       * IconPicker
       * Muestra un grid con todos los íconos de Font Awesome, permitiendo filtrarlos y seleccionar uno.
       */
      const IconPicker = (props) => {
        const { onSelect } = props;
        const [filterText, setFilterText] = useState('');
        const filteredIcons = useMemo(() =>
          ALL_FA_ICONS.filter(ic =>
            ic.name.toLowerCase().indexOf(filterText.toLowerCase()) !== -1
          ), [filterText]);
        return createElement(
          'div',
          { style: { marginBottom: '1em' } },
          createElement(TextControl, {
            label: __('Buscar ícono:', 'asap-theme'),
            value: filterText,
            onChange: setFilterText,
            placeholder: __('Ej: "car", "flag"...', 'asap-theme'),
            style: { marginBottom: '0.5em' }
          }),
          createElement(
            'div',
            {
              style: {
                display: 'flex',
                flexWrap: 'wrap',
                maxHeight: '200px',
                overflowY: 'auto',
                border: '1px solid #ccc',
                borderRadius: '4px',
                padding: '5px'
              }
            },
            filteredIcons.map(iconObj =>
              createElement(
                'button',
                {
                  key: iconObj.class,
                  type: 'button',
                  onClick: () => onSelect(iconObj.class),
                  style: {
                    width: '60px',
                    height: '60px',
                    margin: '5px',
                    border: '1px solid #ccc',
                    background: '#fff',
                    cursor: 'pointer',
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    justifyContent: 'center'
                  }
                },
                createElement('i', {
                  className: iconObj.class,
                  style: { fontSize: '20px', marginBottom: '4px' }
                }),
                createElement('span', { style: { fontSize: '10px', textAlign: 'center' } }, iconObj.name)
              )
            )
          )
        );
      };

      /**
       * Registro del bloque: ASAP − Nota/Aviso
       */
      registerBlockType('asap-theme/note-block', {
        title: __('ASAP − Nota', 'asap-theme'),
        description: __('Muestra avisos destacados combinando textos y un ícono de Font Awesome para captar la atención de tus visitantes. Personaliza colores, tamaños y mensajes para adaptarlos al estilo de tu sitio.', 'asap-theme'),
        icon: 'warning',
        category: 'common',
        keywords: ['asap', 'nota', 'aviso', 'warning', 'info'],

        attributes: {
          noteType: { type: 'string', default: 'info' },
          iconHtml: { type: 'string', default: '<i class="fa fa-info-circle"></i>' },
          noteContent: { type: 'string', default: __('Introduce aquí el contenido de tu nota/aviso.', 'asap-theme') },
          backgroundColor: { type: 'string', default: '' },
          borderColor: { type: 'string', default: '' },
          textColor: { type: 'string', default: '' },
          labelColor: { type: 'string', default: '#767676' },
          paddingSize: { type: 'number', default: 20 },
          fontSize: { type: 'number', default: 18 },
          showIcon: { type: 'boolean', default: true }
        },

        edit: (props) => {
          const { attributes, setAttributes } = props;
          const {
            noteType,
            iconHtml,
            noteContent,
            backgroundColor,
            borderColor,
            textColor,
            paddingSize = 20,
            fontSize = 18,
            showIcon
          } = attributes;

          // Obtiene colores por defecto según el tipo seleccionado
          const def = getDefaultColors(noteType);
          const finalBG = backgroundColor || def.bg;
          const finalBorder = borderColor || def.border;
          const finalText = textColor || def.text;

          // Extraer la clase actual del ícono (si existe)
          const matched = iconHtml.match(/class="([^"]+)"/);
          const currentIconClass = matched ? matched[1] : '';

          const onChangeNoteType = (val) => {
            setAttributes({ noteType: val });
            const icons = {
              info: '<i class="fa fa-info-circle"></i>',
              warning: '<i class="fa fa-exclamation-triangle"></i>',
              success: '<i class="fa fa-check-circle"></i>',
              danger: '<i class="fa fa-times-circle"></i>'
            };
            if (icons[val]) {
              setAttributes({
                iconHtml: icons[val],
                backgroundColor: '',
                borderColor: '',
                textColor: ''
              });
            }
          };

          const onSelectIcon = (newClass) => {
            setAttributes({ iconHtml: newClass ? `<i class="${newClass}"></i>` : '' });
          };

          const onChangeNoteContent = (val) => setAttributes({ noteContent: val || '' });
          const onChangePadding = (val) => setAttributes({ paddingSize: parseInt(val, 10) || 0 });
          const onChangeFontSize = (val) => setAttributes({ fontSize: parseInt(val, 10) || 0 });
          const onToggleShowIcon = (val) => setAttributes({ showIcon: val });

          // Panel de colores personalizado
          const colorPanel = PanelColorSettings && createElement(
            PanelColorSettings,
            {
              title: __('Colores Personalizados', 'asap-theme'),
              initialOpen: false,
              colorSettings: [
                { value: backgroundColor, onChange: (val) => setAttributes({ backgroundColor: val }), label: __('Fondo', 'asap-theme') },
                { value: borderColor, onChange: (val) => setAttributes({ borderColor: val }), label: __('Borde', 'asap-theme') },
                { value: textColor, onChange: (val) => setAttributes({ textColor: val }), label: __('Texto', 'asap-theme') }
              ]
            }
          );

          const inspector = createElement(
            InspectorControls,
            {},
            createElement(
              PanelBody,
              { title: __('Ajustes generales', 'asap-theme'), initialOpen: true },
              createElement(SelectControl, {
                label: __('Tipo de Aviso', 'asap-theme'),
                value: noteType,
                options: [
                  { label: 'Info', value: 'info' },
                  { label: 'Warning', value: 'warning' },
                  { label: 'Success', value: 'success' },
                  { label: 'Danger', value: 'danger' },
                  { label: 'Custom', value: 'custom' }
                ],
                onChange: onChangeNoteType
              }),
              createElement(ToggleControl, {
                label: __('Mostrar ícono', 'asap-theme'),
                checked: showIcon,
                onChange: onToggleShowIcon
              }),
              showIcon && createElement(IconPicker, { onSelect: onSelectIcon })
            ),
            createElement(
              PanelBody,
              { title: __('Tamaños', 'asap-theme'), initialOpen: true },
              createElement(RangeControl, {
                label: __('Tamaño de fuente', 'asap-theme'),
                value: fontSize,
                onChange: onChangeFontSize,
                min: 10,
                max: 100,
                step: 1,
                style: { marginBottom: '10px' }
              }),
              createElement(RangeControl, {
                label: __('Margin interno', 'asap-theme'),
                value: paddingSize,
                onChange: onChangePadding,
                min: 0,
                max: 100,
                step: 1,
                style: { marginBottom: '10px' }
              })
            ),
            colorPanel
          );

          const iconElement = showIcon && iconHtml.trim() &&
            createElement('div', {
              style: { fontSize: '24px', marginRight: '15px', minWidth: '30px' },
              dangerouslySetInnerHTML: { __html: iconHtml }
            });

          const editorUI = createElement(
            'div',
            {
              style: {
                backgroundColor: finalBG,
                border: '2px solid ' + finalBorder,
                color: finalText,
                display: 'flex',
                alignItems: 'center',
                padding: paddingSize + 'px',
                borderRadius: '5px',
                fontSize: fontSize + 'px'
              }
            },
            iconElement,
            createElement(
              'div',
              { style: { flex: '1' } },
              createElement(ManagedRichText, {
                tagName: 'div',
                value: noteContent,
                onChange: onChangeNoteContent
              })
            )
          );

          return createElement(Fragment, {}, inspector, editorUI);
        },

        save: (props) => {
          const { attributes } = props;
          const {
            noteType,
            iconHtml,
            noteContent,
            backgroundColor,
            borderColor,
            textColor,
            paddingSize = 20,
            fontSize = 18,
            showIcon
          } = attributes;

          const def = getDefaultColors(noteType);
          const finalBG = backgroundColor || def.bg;
          const finalBorder = borderColor || def.border;
          const finalText = textColor || def.text;
          const finalContent = removeParagraphTags(noteContent || '');

          const iconDiv = showIcon && iconHtml && iconHtml.trim() &&
            createElement('div', {
              style: { fontSize: '24px', marginRight: '15px', minWidth: '30px' },
              dangerouslySetInnerHTML: { __html: iconHtml }
            });

          return createElement(
            'div',
            {
              className: 'asap-note-block asap-note-' + noteType,
              style: {
                backgroundColor: finalBG,
                border: '2px solid ' + finalBorder,
                color: finalText,
                display: 'flex',
                alignItems: 'center',
                padding: paddingSize + 'px',
                borderRadius: '5px',
                fontSize: fontSize + 'px'
              }
            },
            iconDiv,
            createElement(
              'div',
              { style: { flex: '1' } },
              createElement('div', { dangerouslySetInnerHTML: { __html: finalContent } })
            )
          );
        }
      });
    })( 
      window.wp.blocks,
      window.wp.element,
      window.wp.editor,
      window.wp.components,
      window.wp.i18n,
      window.wp.blockEditor
    );




    (function (blocks, element, editor, components, i18n, blockEditor) {
  const { __ } = i18n;
  const { registerBlockType } = blocks;
  const { Fragment, createElement: el, useState, useEffect, useMemo } = element;
  const { InspectorControls, MediaUpload, RichText } = editor;
  const { PanelBody, Button, ToggleControl, RangeControl } = components;
  const { PanelColorSettings } = blockEditor;

  // Constantes y funciones helper para estilos
  const IMAGE_STYLE = {
    width: '80px',
    height: '80px',
    borderRadius: '50%',
    objectFit: 'cover',
    marginBottom: '10px',
    display: 'block',
    marginLeft: 'auto',
    marginRight: 'auto'
  };

  const getSlideStyle = (cardColor) => ({
    textAlign: 'center',
    padding: '20px',
    boxSizing: 'border-box',
    background: cardColor,
    borderBottom: '1px solid #ddd',
    marginBottom: '10px'
  });

  const getCardStyle = (cardColor, textColor, cardBorderEnabled, cardBorderThickness, cardBorderColor, cardBorderRadius) =>
    cardBorderEnabled
      ? {
          background: cardColor,
          color: textColor,
          padding: '20px',
          border: `${cardBorderThickness}px solid ${cardBorderColor}`,
          borderRadius: `${cardBorderRadius}px`,
          textAlign: 'center'
        }
      : {
          background: cardColor,
          color: textColor,
          padding: '20px',
          borderRadius: '10px',
          boxShadow: '0 4px 8px rgba(0,0,0,0.1)',
          textAlign: 'center'
        };

  // Helper para quitar etiquetas <p>
  const removeParagraphTags = (html = '') =>
    html.replace(/<\/p>/gi, '<br><br>').replace(/<p[^>]*>/gi, '');

  // Componente funcional ManagedSelect utilizando hooks
  const ManagedSelect = (props) => {
    const { value = '', onChange, label, labelStyle, options = [], selectStyle } = props;
    const [currentValue, setCurrentValue] = useState(value);

    useEffect(() => {
      setCurrentValue(value);
    }, [value]);

    const handleChange = (ev) => {
      const val = ev.target.value;
      setCurrentValue(val);
      if (onChange) onChange(val);
    };

    const handleBlur = () => {
      if (onChange) onChange(currentValue);
    };

    return el(
      'div',
      {},
      label && el('label', { style: labelStyle }, label),
      el(
        'select',
        {
          value: currentValue,
          onChange: handleChange,
          onBlur: handleBlur,
          style: selectStyle
        },
        options.map((o) => el('option', { key: o.value, value: o.value }, o.label))
      )
    );
  };

  // Componente funcional de estrellas
  const StarRating = (props) => {
    const { rating = 0, ratingColor = '#FFD700', onChange } = props;
    return el(
      'div',
      { style: { display: 'inline-block', marginTop: '10px' } },
      Array.from({ length: 5 }, (_, index) => {
        const starNumber = index + 1;
        return el(
          'span',
          {
            key: starNumber,
            style: {
              fontSize: '20px',
              cursor: 'pointer',
              color: ratingColor,
              opacity: starNumber <= rating ? 1 : 0.3,
              marginRight: '5px'
            },
            onClick: () => onChange && onChange(starNumber)
          },
          '\u2605'
        );
      })
    );
  };

  // Helper para renderizar el contenido editable de un testimonio (usado en diseños 2 y 3)
  const renderTestimonialEditor = (test, i, updateTestimonial, removeTestimonial, textStyles, titleTag) => {
    return [
      test.photo && el('img', { src: test.photo, style: IMAGE_STYLE }),
      el(MediaUpload, {
        onSelect: (media) => updateTestimonial(i, { photo: media.url }),
        allowedTypes: ['image'],
        render: (obj) =>
          el(
            Button,
            {
              onClick: obj.open,
              isSecondary: true,
              style: { display: 'block', margin: '10px auto' }
            },
            test.photo ? __('Cambiar Foto', 'asap-theme') : __('Subir foto', 'asap-theme')
          )
      }),
      el(RichText, {
        tagName: titleTag,
        value: test.title,
        onChange: (val) => updateTestimonial(i, { title: val }),
        placeholder: __('Título del testimonio', 'asap-theme'),
        style: { margin: '10px 0', fontSize: textStyles.titleFontSize + 'px', color: textStyles.textColor }
      }),
      el(RichText, {
        tagName: 'div',
        value: test.text,
        onChange: (val) => updateTestimonial(i, { text: val }),
        placeholder: __('Texto del testimonio', 'asap-theme'),
        style: { marginBottom: '10px', fontSize: textStyles.textFontSize + 'px', color: textStyles.textColor }
      }),
      el(RichText, {
        tagName: 'div',
        value: test.name,
        onChange: (val) => updateTestimonial(i, { name: val }),
        placeholder: __('Nombre', 'asap-theme'),
        style: { fontWeight: 'bold', fontSize: textStyles.nameFontSize + 'px', marginBottom: '5px', color: textStyles.textColor }
      }),
      el(RichText, {
        tagName: 'div',
        value: test.position,
        onChange: (val) => updateTestimonial(i, { position: val }),
        placeholder: __('Posición / Empresa', 'asap-theme'),
        style: { opacity: 0.8, fontSize: textStyles.positionFontSize + 'px', color: textStyles.textColor }
      }),
      el(StarRating, {
        rating: test.rating,
        ratingColor: textStyles.starColor,
        onChange: (newRating) => updateTestimonial(i, { rating: newRating })
      }),
      el(
        Button,
        {
          isDestructive: true,
          style: { margin: '10px auto', display: 'block' },
          onClick: () => removeTestimonial(i)
        },
        __('Eliminar testimonio', 'asap-theme')
      )
    ];
  };

  // Helpers para la función save: generación de estrellas y procesamiento de textos
  const generateStarsHtml = (ratingVal, starColor) => {
    let starsHtml = '';
    for (let star = 1; star <= 5; star++) {
      starsHtml += `<span style="color:${starColor};font-size:20px;${star <= ratingVal ? '' : 'opacity:0.3;'}">&#9733;</span>`;
    }
    return starsHtml;
  };

  const processText = (text) => removeParagraphTags(text || '');

  const generateTestimonialHtml = (t, textStyles, titleTag) => {
    const finalTitle = processText(t.title);
    const finalText = processText(t.text);
    const finalName = processText(t.name);
    const finalPos = processText(t.position);
    const ratingVal = t.rating || 5;
    const starsHtml = generateStarsHtml(ratingVal, textStyles.starColor);
    const photoHtml = t.photo
      ? `<img src="${t.photo}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:10px;display:block;margin-left:auto;margin-right:auto;" />`
      : '';
    return { photoHtml, finalTitle, finalText, finalName, finalPos, starsHtml };
  };

  // Registro del bloque Testimonial Slider (ahora sin título)
  registerBlockType('asap-theme/testimonial-slider', {
    title: __('ASAP − Testimonios', 'asap-theme'),
    description: __('Muestra testimonios en diseños modernos, con fotos, calificación y Schema para SEO.', 'asap-theme'),
    icon: 'format-quote',
    category: 'common',
    keywords: ['asap', 'testimonial', 'slider', 'review', 'schema'],
    attributes: {
      // Se elimina sliderTitle para no mostrar título
      testimonials: {
        type: 'array',
        default: [
          {
            name: __('Juan Pérez', 'asap-theme'),
            position: __('CEO en Empresa X', 'asap-theme'),
            photo: '',
            title: __('Excelente servicio', 'asap-theme'),
            text: __('Estoy muy satisfecho con el producto, lo recomiendo ampliamente.', 'asap-theme'),
            rating: 5
          },
          {
            name: __('María Gómez', 'asap-theme'),
            position: __('Freelancer', 'asap-theme'),
            photo: '',
            title: __('Muy buena atención', 'asap-theme'),
            text: __('Me sentí acompañada todo el tiempo, el soporte es rápido y efectivo.', 'asap-theme'),
            rating: 4
          }
        ]
      },
      bgColor: { type: 'string', default: '#ffffff' },
      textColor: { type: 'string', default: '#181818' },
      accentColor: { type: 'string', default: '#2B6CB0' },
      starColor: { type: 'string', default: '#FFD700' },
      cardBorderColor: { type: 'string', default: '#2B6CB0' },
      cardColor: { type: 'string', default: '#ffffff' },
      testimonialNameFontSize: { type: 'number', default: 18 },
      testimonialPositionFontSize: { type: 'number', default: 16 },
      testimonialTitleFontSize: { type: 'number', default: 20 },
      testimonialTextFontSize: { type: 'number', default: 17 },
      design: { type: 'string', default: 'design1' },
      columnsDesktop: { type: 'number', default: 3 },
      cardBorderEnabled: { type: 'boolean', default: false },
      cardBorderThickness: { type: 'number', default: 1 },
      cardBorderRadius: { type: 'number', default: 10 },
      titleTag: { type: 'string', default: 'h3' },
      // Nuevo atributo para el identificador único del slider
      sliderId: { type: 'string', default: '' }
    },
    edit: (props) => {
      const { attributes, setAttributes } = props;
      const {
        testimonials = [],
        bgColor,
        textColor,
        accentColor,
        starColor,
        cardBorderColor,
        cardColor,
        testimonialNameFontSize,
        testimonialPositionFontSize,
        testimonialTitleFontSize,
        testimonialTextFontSize,
        design,
        columnsDesktop,
        cardBorderEnabled,
        cardBorderThickness,
        cardBorderRadius,
        titleTag,
        sliderId
      } = attributes;

      // Si no existe un sliderId, generamos uno único
      if (!sliderId) {
        const newId = 'asap-slider-' + Math.random().toString(36).substr(2, 9);
        setAttributes({ sliderId: newId });
      }

      const textStyles = {
        textColor,
        nameFontSize: testimonialNameFontSize,
        positionFontSize: testimonialPositionFontSize,
        titleFontSize: testimonialTitleFontSize,
        textFontSize: testimonialTextFontSize,
        starColor
      };

      const updateTestimonial = (index, newData) => {
        const newTestimonials = testimonials.slice();
        newTestimonials[index] = { ...newTestimonials[index], ...newData };
        setAttributes({ testimonials: newTestimonials });
      };

      const addTestimonial = () => {
        setAttributes({
          testimonials: [
            ...testimonials,
            {
              name: __('Nuevo Nombre', 'asap-theme'),
              position: '',
              photo: '',
              title: __('Nuevo Testimonio', 'asap-theme'),
              text: '',
              rating: 5
            }
          ]
        });
      };

      const removeTestimonial = (index) => {
        const newTestimonials = testimonials.filter((_, i) => i !== index);
        setAttributes({ testimonials: newTestimonials });
      };

      // Memoización de las diapositivas para diseño1
      const slides = useMemo(() => {
        return testimonials.map((test, i) => {
          return el(
            'div',
            {
              className: 'asap-slide',
              key: i,
              style: getSlideStyle(cardColor)
            },
            test.photo && el('img', { src: test.photo, style: IMAGE_STYLE }),
            el(MediaUpload, {
              onSelect: (media) => updateTestimonial(i, { photo: media.url }),
              allowedTypes: ['image'],
              render: (obj) =>
                el(
                  Button,
                  {
                    onClick: obj.open,
                    isSecondary: true,
                    style: { display: 'block', margin: '10px auto' }
                  },
                  test.photo ? __('Cambiar Foto', 'asap-theme') : __('Subir foto', 'asap-theme')
                )
            }),
            el(RichText, {
              tagName: titleTag,
              value: test.title,
              onChange: (val) => updateTestimonial(i, { title: val }),
              placeholder: __('Título del testimonio', 'asap-theme'),
              style: { margin: '10px 0', fontSize: testimonialTitleFontSize + 'px', color: textColor }
            }),
            el(RichText, {
              tagName: 'div',
              value: test.text,
              onChange: (val) => updateTestimonial(i, { text: val }),
              placeholder: __('Texto del testimonio', 'asap-theme'),
              style: { marginBottom: '10px', fontSize: testimonialTextFontSize + 'px', color: textColor }
            }),
            el(RichText, {
              tagName: 'div',
              value: test.name,
              onChange: (val) => updateTestimonial(i, { name: val }),
              placeholder: __('Nombre', 'asap-theme'),
              style: { fontWeight: 'bold', fontSize: testimonialNameFontSize + 'px', marginBottom: '5px', color: textColor }
            }),
            el(RichText, {
              tagName: 'div',
              value: test.position,
              onChange: (val) => updateTestimonial(i, { position: val }),
              placeholder: __('Posición / Empresa', 'asap-theme'),
              style: { opacity: 0.8, fontSize: testimonialPositionFontSize + 'px', color: textColor }
            }),
            el(StarRating, {
              rating: test.rating,
              ratingColor: starColor,
              onChange: (newRating) => updateTestimonial(i, { rating: newRating })
            }),
            el(
              Button,
              {
                isDestructive: true,
                style: { margin: '10px auto', display: 'block' },
                onClick: () => removeTestimonial(i)
              },
              __('Eliminar testimonio', 'asap-theme')
            )
          );
        });
      }, [testimonials, cardColor, textColor, testimonialTitleFontSize, testimonialTextFontSize, testimonialNameFontSize, testimonialPositionFontSize, starColor, titleTag]);

      let previewContent = null;
      if (design === 'design1') {
        // Diseño slider sin título; se añade el id único en el contenedor
        previewContent = el(
          'div',
          {
            id: sliderId,
            className: 'asap-testimonial-slider',
            style: {
              backgroundColor: bgColor,
              color: textColor,
              padding: '20px',
              borderRadius: '5px',
              textAlign: 'center'
            }
          },
          el('div', { className: 'asap-slides', style: { display: 'flex', flexDirection: 'column' } }, slides)
        );
      } else if (design === 'design2') {
        previewContent = el(
          'div',
          {
            className: 'asap-testimonial-cards',
            style: {
              backgroundColor: bgColor,
              color: textColor,
              padding: '20px',
              borderRadius: '5px',
              marginBottom: '2rem'
            }
          },
          el(
            'div',
            {
              style: {
                display: 'grid',
                gridTemplateColumns: `repeat(${columnsDesktop}, 1fr)`,
                gap: '20px'
              }
            },
            testimonials.map((test, i) => {
              const cardStyle = getCardStyle(cardColor, textColor, cardBorderEnabled, cardBorderThickness, cardBorderColor, cardBorderRadius);
              return el(
                'div',
                { key: i, className: 'asap-card', style: cardStyle },
                renderTestimonialEditor(test, i, updateTestimonial, removeTestimonial, textStyles, titleTag)
              );
            })
          )
        );
      } else if (design === 'design3') {
        previewContent = el(
          'div',
          {
            className: 'asap-testimonial-pinterest',
            style: {
              backgroundColor: bgColor,
              color: textColor,
              borderRadius: '5px',
              marginBottom: '2rem'
            }
          },
          el(
            'div',
            { style: { columnCount: columnsDesktop, columnGap: '20px' } },
            testimonials.map((test, i) => {
              const cardStyle = getCardStyle(cardColor, textColor, cardBorderEnabled, cardBorderThickness, cardBorderColor, cardBorderRadius);
              return el(
                'div',
                { key: i, className: 'asap-card', style: { ...cardStyle, breakInside: 'avoid', marginBottom: '20px' } },
                renderTestimonialEditor(test, i, updateTestimonial, removeTestimonial, textStyles, titleTag)
              );
            })
          )
        );
      }

      const inspectorControls = el(
        InspectorControls,
        {},
        el(
          PanelBody,
          { title: __('Diseño', 'asap-theme'), initialOpen: true },
          el(ManagedSelect, {
            value: design,
            onChange: (newVal) => setAttributes({ design: newVal }),
            options: [
              { value: 'design1', label: __('Slider', 'asap-theme') },
              { value: 'design2', label: __('Tarjetas', 'asap-theme') },
              { value: 'design3', label: __('Pinterest', 'asap-theme') }
            ]
          }),
          (design === 'design2' || design === 'design3') &&
            el(RangeControl, {
              label: __('Número de Columnas', 'asap-theme'),
              value: columnsDesktop,
              onChange: (val) => setAttributes({ columnsDesktop: val }),
              min: 1,
              max: 6
            }),
          (design === 'design2' || design === 'design3') &&
            el(ToggleControl, {
              label: __('Usar borde en lugar de sombra', 'asap-theme'),
              checked: cardBorderEnabled,
              onChange: (val) => setAttributes({ cardBorderEnabled: val })
            }),
          (design === 'design2' || design === 'design3') &&
            cardBorderEnabled &&
            el(RangeControl, {
              label: __('Grosor del Borde (px)', 'asap-theme'),
              value: cardBorderThickness,
              onChange: (val) => setAttributes({ cardBorderThickness: val }),
              min: 0,
              max: 20
            }),
          (design === 'design2' || design === 'design3') &&
            cardBorderEnabled &&
            el(RangeControl, {
              label: __('Radio del Borde (px)', 'asap-theme'),
              value: cardBorderRadius,
              onChange: (val) => setAttributes({ cardBorderRadius: val }),
              min: 0,
              max: 50
            })
        ),
        el(
          PanelBody,
          { title: __('Tamaños', 'asap-theme'), initialOpen: false },
          el(RangeControl, {
            label: __('Tamaño del Título', 'asap-theme'),
            value: testimonialTitleFontSize,
            onChange: (val) => setAttributes({ testimonialTitleFontSize: val }),
            min: 10,
            max: 50
          }),
          el(RangeControl, {
            label: __('Tamaño de la Descripción', 'asap-theme'),
            value: testimonialTextFontSize,
            onChange: (val) => setAttributes({ testimonialTextFontSize: val }),
            min: 10,
            max: 50
          }),
          el(RangeControl, {
            label: __('Tamaño del Nombre', 'asap-theme'),
            value: testimonialNameFontSize,
            onChange: (val) => setAttributes({ testimonialNameFontSize: val }),
            min: 10,
            max: 50
          }),
          el(RangeControl, {
            label: __('Tamaño de la Posición/Empresa', 'asap-theme'),
            value: testimonialPositionFontSize,
            onChange: (val) => setAttributes({ testimonialPositionFontSize: val }),
            min: 10,
            max: 50
          })
        ),
        PanelColorSettings &&
          el(PanelColorSettings, {
            title: __('Colores', 'asap-theme'),
            initialOpen: false,
            colorSettings: [
              {
                value: bgColor,
                onChange: (val) => setAttributes({ bgColor: val }),
                label: __('Fondo', 'asap-theme')
              },
              {
                value: cardColor,
                onChange: (val) => setAttributes({ cardColor: val }),
                label: __('Caja de testimonio', 'asap-theme')
              },
              {
                value: textColor,
                onChange: (val) => setAttributes({ textColor: val }),
                label: __('Texto', 'asap-theme')
              },
              {
                value: accentColor,
                onChange: (val) => setAttributes({ accentColor: val }),
                label: __('Bullets de navegación', 'asap-theme')
              },
              {
                value: starColor,
                onChange: (val) => setAttributes({ starColor: val }),
                label: __('Estrellas', 'asap-theme')
              },
              {
                value: cardBorderColor,
                onChange: (val) => setAttributes({ cardBorderColor: val }),
                label: __('Borde', 'asap-theme')
              }
            ]
          })
      );

      return el(
        Fragment,
        {},
        inspectorControls,
        previewContent,
        el(
          Button,
          {
            isPrimary: true,
            style: { marginTop: '10px' },
            onClick: addTestimonial
          },
          __('Añadir Testimonio', 'asap-theme')
        )
      );
    },
    save: (props) => {
      const { attributes } = props;
      const {
        testimonials = [],
        bgColor = '#ffffff',
        textColor = '#000000',
        accentColor = '#2B6CB0',
        starColor = '#FFD700',
        cardBorderColor = '#2B6CB0',
        cardColor = '#ffffff',
        testimonialNameFontSize = 18,
        testimonialPositionFontSize = 16,
        testimonialTitleFontSize = 20,
        testimonialTextFontSize = 14,
        design = 'design1',
        columnsDesktop = 3,
        cardBorderEnabled,
        cardBorderThickness,
        cardBorderRadius,
        titleTag = 'h4',
        sliderId
      } = attributes;

      // Para seguridad, si no existe sliderId se genera uno
      const finalSliderId = sliderId || 'asap-slider-' + Math.random().toString(36).substr(2, 9);

      const textStyles = {
        textColor,
        nameFontSize: testimonialNameFontSize,
        positionFontSize: testimonialPositionFontSize,
        titleFontSize: testimonialTitleFontSize,
        textFontSize: testimonialTextFontSize,
        starColor
      };

      let finalHtml = '';

      if (design === 'design1') {
        let slidesHtml = '';
        testimonials.forEach((t) => {
          const { photoHtml, finalTitle, finalText, finalName, finalPos, starsHtml } = generateTestimonialHtml(t, textStyles, titleTag);
          slidesHtml += `<div class="asap-slide" style="flex:0 0 100%;text-align:center;padding:20px;box-sizing:border-box;background:${cardColor};border-bottom:1px solid #ddd;margin-bottom:10px;">` +
                        photoHtml +
                        `<${titleTag} style="margin:10px 0 16px;font-size:${testimonialTitleFontSize}px; color:${textColor};">${finalTitle}</${titleTag}>` +
                        `<div style="line-height: 1.4;margin-bottom:16px;font-size:${testimonialTextFontSize}px; color:${textColor};">${finalText}</div>` +
                        `<div style="font-weight:bold;font-size:${testimonialNameFontSize}px; color:${textColor};">${finalName}</div>` +
                        (finalPos
                          ? `<div style="margin-top:8px; opacity:0.8;font-size:${testimonialPositionFontSize}px; color:${textColor};">${finalPos}</div>`
                          : '') +
                        `<div style="margin-top:10px;">${starsHtml}</div>` +
                        `</div>`;
        });
        let navHtml = `<div class="asap-nav" style="text-align:center;margin-top:15px;">`;
        testimonials.forEach((_, index) => {
          navHtml += `<label style="background:${accentColor};display:inline-block;width:12px;height:12px;border-radius:50%;margin:3px;cursor:pointer;${index === 0 ? '' : 'opacity:0.5;'}"></label>`;
        });
        navHtml += `</div>`;
        const sliderWrapper = `<div class="asap-slider-wrapper" style="overflow:hidden;position:relative;">` +
                              `<div class="asap-slides" style="display:flex;transition:transform 0.6s ease;">${slidesHtml}</div>` +
                              `</div>`;
        // Se añade el id único al contenedor del slider
        finalHtml = `<div id="${finalSliderId}" class="asap-testimonial-slider" style="background:${bgColor};color:${textColor};padding:20px;border-radius:5px;text-align:center;overflow:hidden;position:relative;">` +
                    sliderWrapper +
                    navHtml +
                    `</div>`;
        // El script ahora busca el slider usando su id único
        const sliderScript = `<script>(function(){document.addEventListener("DOMContentLoaded",function(){` +
                             `var slider=document.getElementById("${finalSliderId}");if(!slider)return;` +
                             `var slides=slider.querySelector(".asap-slides"),slideItems=slides.children,navBullets=slider.querySelectorAll(".asap-nav label"),currentIndex=0;` +
                             `function goToSlide(index){if(index<0){index=slideItems.length-1;}else if(index>=slideItems.length){index=0;}currentIndex=index;` +
                             `slides.style.transform="translateX(-"+(currentIndex*100)+"%)";` +
                             `Array.prototype.forEach.call(navBullets,function(bullet,i){bullet.style.opacity=(i===currentIndex)?"1":"0.5";});}` +
                             `Array.prototype.forEach.call(navBullets,function(bullet,index){bullet.addEventListener("click",function(){goToSlide(index);});});` +
                             `});})();</script>`;
        finalHtml += sliderScript;
      } else if (design === 'design2') {
        let cardsHtml = '';
        testimonials.forEach((t) => {
          const { photoHtml, finalTitle, finalText, finalName, finalPos, starsHtml } = generateTestimonialHtml(t, textStyles, titleTag);
          cardsHtml += `<div class="asap-card" style="${cardBorderEnabled
            ? `background:${cardColor};color:${textColor};padding:20px;border:${cardBorderThickness}px solid ${cardBorderColor};border-radius:${cardBorderRadius}px;text-align:center;`
            : `background:${cardColor};color:${textColor};padding:20px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);text-align:center;`}">` +
                       photoHtml +
                       `<${titleTag} style="margin:10px 0 16px;font-size:${testimonialTitleFontSize}px; color:${textColor};">${finalTitle}</${titleTag}>` +
                       `<div style="line-height: 1.4;margin-bottom:16px;font-size:${testimonialTextFontSize}px; color:${textColor};">${finalText}</div>` +
                       `<div style="font-weight:bold;font-size:${testimonialNameFontSize}px; color:${textColor};">${finalName}</div>` +
                       (finalPos
                         ? `<div style="margin-top:8px; opacity:0.8;font-size:${testimonialPositionFontSize}px; color:${textColor};">${finalPos}</div>`
                         : '') +
                       `<div style="margin-top:10px;">${starsHtml}</div>` +
                       `</div>`;
        });
        finalHtml = `<div class="asap-testimonial-cards" style="background:${bgColor};color:${textColor};margin-bottom:2rem;border-radius:5px;">` +
                    `<div style="display:grid; grid-template-columns:repeat(${columnsDesktop}, 1fr); gap:26px;">` +
                    cardsHtml +
                    `</div></div>`;
      } else if (design === 'design3') {
        let gridCardsHtml = '';
        testimonials.forEach((t) => {
          const { photoHtml, finalTitle, finalText, finalName, finalPos, starsHtml } = generateTestimonialHtml(t, textStyles, titleTag);
          gridCardsHtml += `<div class="asap-card" style="${cardBorderEnabled
            ? `background:${cardColor};color:${textColor};padding:20px;border:${cardBorderThickness}px solid ${cardBorderColor};border-radius:${cardBorderRadius}px;text-align:center;break-inside:avoid;margin-bottom:20px;`
            : `background:${cardColor};color:${textColor};padding:20px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);text-align:center;break-inside:avoid;margin-bottom:20px;`}">` +
                           photoHtml +
                           `<${titleTag} style="margin:10px 0 16px;font-size:${testimonialTitleFontSize}px; color:${textColor};">${finalTitle}</${titleTag}>` +
                           `<div style="line-height: 1.4;margin-bottom:16px;font-size:${testimonialTextFontSize}px; color:${textColor};">${finalText}</div>` +
                           `<div style="font-weight:bold;font-size:${testimonialNameFontSize}px; color:${textColor};">${finalName}</div>` +
                           (finalPos
                             ? `<div style="margin-top:8px; opacity:0.8;font-size:${testimonialPositionFontSize}px; color:${textColor};">${finalPos}</div>`
                             : '') +
                           `<div style="margin-top:10px;">${starsHtml}</div>` +
                           `</div>`;
        });
        finalHtml = `<div class="asap-testimonial-pinterest" style="background:${bgColor};color:${textColor};padding:20px;border-radius:5px;">` +
                    `<div style="column-count:${columnsDesktop}; column-gap:26px;">` +
                    gridCardsHtml +
                    `</div></div>`;
      }
      return el(Fragment, {}, el('div', { dangerouslySetInnerHTML: { __html: finalHtml } }));
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);



            



    /**
     * ======================================================
     *  BLOQUE: “ASAP − FAQ con acordeón”
     * ======================================================
     */
    (function (blocks, element, editor, components, i18n, blockEditor) {
      const { __ } = i18n;
      const { registerBlockType } = blocks;
      const { Fragment, createElement: el, Component, useState, useMemo, memo } = element;
      const { InspectorControls, RichText } = editor;
      const { PanelBody, RangeControl, SelectControl, CheckboxControl, Button, TextControl } = components;
      const { PanelColorSettings } = blockEditor;

      // Helper functions para estilos comunes en el resumen e íconos
      function getSummaryStyle({ iconLeft, questionPaddingVertical, questionPaddingHorizontal, designStyle, questionBackgroundColor }) {
        const baseStyle = {
          display: 'flex',
          alignItems: 'center',
          fontWeight: 'bold',
          padding: `${questionPaddingVertical}px ${questionPaddingHorizontal}px`,
          justifyContent: iconLeft ? 'flex-start' : 'space-between'
        };
        if (designStyle === 'iconRight' || designStyle === 'card') {
          baseStyle.background = questionBackgroundColor;
        }
        return baseStyle;
      }

      // Actualizamos getIconStyle para incluir el color del ícono (usando iconColor)
      function getIconStyle(iconLeft, iconColor) {
        return { 
          display: 'inline-block',
          color: iconColor,
          ...(iconLeft ? { marginRight: '8px' } : { marginLeft: '8px' })
        };
      }

      // IconPicker: componente funcional optimizado con memoización del filtrado
      const IconPicker = memo(({ onSelect }) => {
        const [filter, setFilter] = useState('');
        
        const handleFilterChange = (val) => {
          setFilter(val.toLowerCase());
        };

        const filteredIcons = useMemo(() => {
          return ALL_FA_ICONS.filter(ic => ic.name.toLowerCase().includes(filter));
        }, [filter]);

        // Estilos constantes para IconPicker
        const containerStyle = { border: '1px solid #ccc', padding: '6px', borderRadius: '4px', marginBottom: '8px' };
        const inputStyle = { marginBottom: '6px' };
        const iconsContainerStyle = { display: 'flex', flexWrap: 'wrap', maxHeight: '160px', overflowY: 'auto' };
        const buttonStyle = {
          width: '60px',
          height: '60px',
          margin: '3px',
          border: '1px solid #ddd',
          borderRadius: '4px',
          background: '#fff',
          cursor: 'pointer',
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center'
        };
        const iconStyle = { fontSize: '20px', marginBottom: '4px' };
        const spanStyle = { fontSize: '10px', textAlign: 'center' };

        return el(
          'div',
          { style: containerStyle },
          el(TextControl, {
            label: __('Buscar ícono', 'asap-theme'),
            value: filter,
            onChange: handleFilterChange,
            style: inputStyle
          }),
          el(
            'div',
            { style: iconsContainerStyle },
            filteredIcons.map(obj =>
              el(
                'button',
                {
                  key: obj.class,
                  type: 'button',
                  onClick: () => onSelect(`<i class="${obj.class}"></i>`),
                  style: buttonStyle
                },
                el('i', { className: obj.class, style: iconStyle }),
                el('span', { style: spanStyle }, obj.name)
              )
            )
          )
        );
      });

      // Función para aplicar tamaño al primer <span> o <i> del HTML del ícono.
      const applyIconSize = (iconHTML, sizePx) => {
        if (!iconHTML) return '';
        const styleStr = `display:inline-block;width:${sizePx}px;height:${sizePx}px;line-height:${sizePx}px;font-size:${sizePx}px;`;
        return iconHTML.replace(/(<(span|i)\b[^>]*)(>)/i, `$1 style="${styleStr}"$3`);
      };

      // Atributos del bloque FAQ
      const blockAttributes = {
        backgroundColor: { type: 'string', default: '#ffffff' },
        questionTextColor: { type: 'string', default: '#181818' },
        answerTextColor: { type: 'string', default: '#181818' },
        faqs: {
          type: 'array',
          default: [
            {
              question: __('¿Qué es un nicho de mercado?', 'asap-theme'),
              answer: __(
                'Un nicho de mercado es un segmento específico del mercado con intereses y necesidades particulares.',
                'asap-theme'
              )
            },
            {
              question: __('¿Por qué es importante el SEO?', 'asap-theme'),
              answer: __(
                'El SEO ayuda a que tu contenido aparezca en los primeros resultados de búsqueda, atrayendo más tráfico y clientes potenciales.',
                'asap-theme'
              )
            }
          ]
        },
        designStyle: { type: 'string', default: 'default' }, // 'default', 'iconRight', 'card'
        questionBackgroundColor: { type: 'string', default: '#f0f0f0' },
        questionTag: { type: 'string', default: 'h4' },
        iconLeft: { type: 'boolean', default: false },
        questionPaddingVertical: { type: 'number', default: 10 },
        questionPaddingHorizontal: { type: 'number', default: 20 },
        answerPaddingVertical: { type: 'number', default: 10 },
        answerPaddingHorizontal: { type: 'number', default: 20 },
        selectedIcon: { type: 'string', default: '<i class="fa fa-chevron-down"></i>' },
        useTransition: { type: 'boolean', default: true },
        rotateIcons: { type: 'boolean', default: true },
        containerPadding: { type: 'number', default: 0 },
        questionSize: { type: 'number', default: 20 },
        answerSize: { type: 'number', default: 16 },
        // Nuevo atributo: iconColor. El valor por defecto se establece igual que questionTextColor.
        iconColor: { type: 'string', default: '#181818' }
      };

      registerBlockType('asap-theme/faq-accordion', {
        title: __('ASAP − FAQ con acordeón', 'asap-theme'),
        description: __('Bloque de FAQ con acordeón y marcado Schema FAQPage para SEO.', 'asap-theme'),
        icon: 'editor-help',
        category: 'common',
        keywords: ['faq', 'acordeon', 'schema', 'asap', 'seo'],
        attributes: blockAttributes,

        edit: (props) => {
          const { attributes, setAttributes } = props;
          const {
            backgroundColor,
            questionTextColor,
            answerTextColor,
            faqs,
            designStyle,
            questionBackgroundColor,
            questionTag,
            iconLeft,
            questionPaddingVertical,
            questionPaddingHorizontal,
            answerPaddingVertical,
            answerPaddingHorizontal,
            selectedIcon,
            useTransition,
            rotateIcons,
            containerPadding,
            questionSize,
            answerSize,
            iconColor
          } = attributes;

          // Manejo de FAQs
          const onChangeFAQ = (index, newFAQ) => {
            const newFAQs = [...faqs];
            newFAQs[index] = newFAQ;
            setAttributes({ faqs: newFAQs });
          };
          const addNewFAQ = () => {
            setAttributes({
              faqs: [
                ...faqs,
                {
                  question: __('Nueva pregunta', 'asap-theme'),
                  answer: __('Nueva respuesta', 'asap-theme')
                }
              ]
            });
          };

          // Cambios de atributos
          const onBackgroundColorChange = (val) => setAttributes({ backgroundColor: val });
          const onQuestionTextColorChange = (val) => setAttributes({ questionTextColor: val });
          const onAnswerTextColorChange = (val) => setAttributes({ answerTextColor: val });
          const onDesignStyleChange = (val) => setAttributes({ designStyle: val });
          const onQuestionBackgroundColorChange = (val) => setAttributes({ questionBackgroundColor: val });
          const onQuestionTagChange = (val) => setAttributes({ questionTag: val });
          const onIconLeftChange = (val) => setAttributes({ iconLeft: val });
          const onQuestionPaddingVerticalChange = (val) => setAttributes({ questionPaddingVertical: val });
          const onQuestionPaddingHorizontalChange = (val) => setAttributes({ questionPaddingHorizontal: val });
          const onAnswerPaddingVerticalChange = (val) => setAttributes({ answerPaddingVertical: val });
          const onAnswerPaddingHorizontalChange = (val) => setAttributes({ answerPaddingHorizontal: val });
          const onSelectIcon = (iconHTML) => setAttributes({ selectedIcon: iconHTML });
          const onClearIcon = () => setAttributes({ selectedIcon: '' });
          const onUseTransitionChange = (val) => setAttributes({ useTransition: val });
          const onRotateIconsChange = (val) => setAttributes({ rotateIcons: val });
          const onContainerPaddingChange = (val) => setAttributes({ containerPadding: val });
          const onQuestionSizeChange = (val) => setAttributes({ questionSize: val });
          const onAnswerSizeChange = (val) => setAttributes({ answerSize: val });
          const onIconColorChange = (val) => setAttributes({ iconColor: val });

          // Render del <summary> en el editor (deshabilitamos el toggle para que siempre esté expandido)
          const renderSummary = (faqItem, idx) => {
            const summaryStyle = getSummaryStyle({
              iconLeft,
              questionPaddingVertical,
              questionPaddingHorizontal,
              designStyle,
              questionBackgroundColor
            });
            const iconStyle = getIconStyle(iconLeft, iconColor);
            const iconEl = selectedIcon
              ? el('span', {
                  className: 'faq-arrow',
                  style: iconStyle,
                  dangerouslySetInnerHTML: { __html: applyIconSize(selectedIcon, 20) }
                })
              : el('span', { className: 'faq-arrow', style: iconStyle }, iconLeft ? '▶' : '◀');
            // Se añade onClick preventivo para evitar toggles en el editor
            const questionEl = el(RichText, {
              tagName: questionTag,
              value: faqItem.question,
              onChange: (newVal) => onChangeFAQ(idx, { ...faqItem, question: newVal }),
              placeholder: __('Pregunta...', 'asap-theme'),
              style: { margin: 0, flex: '1 1 auto', fontSize: `${questionSize}px`, color: questionTextColor }
            });
            return el('summary', { style: summaryStyle, onClick: (e) => e.preventDefault() },
              iconLeft ? [iconEl, questionEl] : [questionEl, iconEl]
            );
          };

          // Previsualización en el editor: usamos <details open> pero prevenimos el toggle.
          const faqPreview = faqs.map((faqItem, idx) => {
            let detailsStyle;
            if (designStyle === 'default') {
              detailsStyle = { marginBottom: '10px', borderBottom: '1px solid #ccc', paddingBottom: '10px' };
            } else if (designStyle === 'iconRight') {
              detailsStyle = { marginBottom: '10px' };
            } else if (designStyle === 'card') {
              detailsStyle = {
                marginBottom: '20px',
                border: '1px solid #ddd',
                borderRadius: '5px',
                boxShadow: '0 2px 5px rgba(0,0,0,0.1)',
                overflow: 'hidden'
              };
            }
            return el(
              'details',
              { key: idx, open: true, style: detailsStyle },
              renderSummary(faqItem, idx),
              el(
                'div',
                { style: { padding: `${answerPaddingVertical}px ${answerPaddingHorizontal}px` } },
                el(RichText, {
                  tagName: 'p',
                  value: faqItem.answer,
                  onChange: (newVal) => onChangeFAQ(idx, { ...faqItem, answer: newVal }),
                  placeholder: __('Respuesta...', 'asap-theme'),
                  style: { fontSize: `${answerSize}px`, color: answerTextColor }
                })
              )
            );
          });

          // Panel de colores. Condicional: "Fondo pregunta" solo se muestra si designStyle no es 'default'.
          const colorSettings = [
            { label: __('Fondo sección', 'asap-theme'), value: backgroundColor, onChange: onBackgroundColorChange },
            designStyle !== 'default' && { label: __('Fondo pregunta', 'asap-theme'), value: questionBackgroundColor, onChange: onQuestionBackgroundColorChange },
            { label: __('Texto pregunta', 'asap-theme'), value: questionTextColor, onChange: onQuestionTextColorChange },
            { label: __('Texto respuesta', 'asap-theme'), value: answerTextColor, onChange: onAnswerTextColorChange },
            { label: __('Icono', 'asap-theme'), value: iconColor, onChange: onIconColorChange }
          ].filter(Boolean);

          const colorPanel = el(
            PanelColorSettings,
            {
              title: __('Colores', 'asap-theme'),
              initialOpen: true,
              colorSettings: colorSettings
            }
          );

          // Panel de diseño
          const designPanel = el(
            PanelBody,
            { title: __('Ajustes', 'asap-theme'), initialOpen: false },
            el(SelectControl, {
              label: __('Tipo de diseño', 'asap-theme'),
              value: designStyle,
              options: [
                { label: 'Diseño 1', value: 'default' },
                { label: 'Diseño 2', value: 'iconRight' },
                { label: 'Diseño 3', value: 'card' }
              ],
              onChange: onDesignStyleChange
            }),
            el(SelectControl, {
              label: __('Etiqueta de la Pregunta', 'asap-theme'),
              value: questionTag,
              options: [
                { label: 'H2', value: 'h2' },
                { label: 'H3', value: 'h3' },
                { label: 'H4', value: 'h4' },
                { label: 'P', value: 'p' }
              ],
              onChange: onQuestionTagChange
            }),
            el(CheckboxControl, {
              label: __('Mostrar icono a Izquierda', 'asap-theme'),
              checked: iconLeft,
              onChange: onIconLeftChange
            }),
            el(CheckboxControl, {
              label: __('Rotar íconos automáticamente', 'asap-theme'),
              checked: rotateIcons,
              onChange: onRotateIconsChange
            }),
            el(RangeControl, {
              label: __('Padding contenedor', 'asap-theme'),
              value: containerPadding,
              onChange: onContainerPaddingChange,
              min: 0,
              max: 50
            }),
            el(RangeControl, {
              label: __('Padding vertical preguntas', 'asap-theme'),
              value: questionPaddingVertical,
              onChange: onQuestionPaddingVerticalChange,
              min: 0,
              max: 50
            }),
            el(RangeControl, {
              label: __('Padding Horizontal Preguntas', 'asap-theme'),
              value: questionPaddingHorizontal,
              onChange: onQuestionPaddingHorizontalChange,
              min: 0,
              max: 50
            }),
            el(RangeControl, {
              label: __('Padding Vertical Respuestas', 'asap-theme'),
              value: answerPaddingVertical,
              onChange: onAnswerPaddingVerticalChange,
              min: 0,
              max: 50
            }),
            el(RangeControl, {
              label: __('Padding Horizontal Respuestas', 'asap-theme'),
              value: answerPaddingHorizontal,
              onChange: onAnswerPaddingHorizontalChange,
              min: 0,
              max: 50
            })
          );

          // Panel de tamaños
          const sizePanel = el(
            PanelBody,
            { title: __('Tamaños', 'asap-theme'), initialOpen: false },
            el(RangeControl, {
              label: __('Tamaño preguntas', 'asap-theme'),
              value: questionSize,
              onChange: onQuestionSizeChange,
              min: 10,
              max: 40
            }),
            el(RangeControl, {
              label: __('Tamaño Respuestas', 'asap-theme'),
              value: answerSize,
              onChange: onAnswerSizeChange,
              min: 10,
              max: 40
            })
          );

          const inspector = el(InspectorControls, {}, colorPanel, designPanel, sizePanel);

          return el(
            Fragment,
            {},
            inspector,
            el(
              'div',
              { className: 'asap-faq-accordion-editor', style: { backgroundColor, padding: `${containerPadding}px`, marginBottom: '2rem' } },
              faqPreview,
              el(
                Button,
                { isPrimary: true, onClick: addNewFAQ, style: { marginTop: '10px' } },
                __('+ Añadir pregunta', 'asap-theme')
              )
            )
          );
        },

        save: (props) => {
          const { attributes } = props;
          const {
            backgroundColor,
            questionTextColor,
            answerTextColor,
            faqs,
            designStyle,
            questionBackgroundColor,
            questionTag,
            iconLeft,
            questionPaddingVertical,
            questionPaddingHorizontal,
            answerPaddingVertical,
            answerPaddingHorizontal,
            selectedIcon,
            useTransition,
            rotateIcons,
            containerPadding,
            questionSize,
            answerSize,
            iconColor
          } = attributes;

          // Markup JSON-LD para Schema FAQPage
          const faqJson = faqs.reduce((acc, f) => {
            if (f.question && f.answer) {
              acc.push({
                '@type': 'Question',
                name: f.question,
                acceptedAnswer: { '@type': 'Answer', text: f.answer }
              });
            }
            return acc;
          }, []);
          const schemaData = faqJson.length > 0
            ? { '@context': 'https://schema.org', '@type': 'FAQPage', mainEntity: faqJson }
            : null;

          // Renderizado estático del <summary> en el frontend usando helper para estilos
          const renderSummaryStatic = (faqItem) => {
            const summaryStyle = getSummaryStyle({
              iconLeft,
              questionPaddingVertical,
              questionPaddingHorizontal,
              designStyle,
              questionBackgroundColor
            });
            const iconStyle = getIconStyle(iconLeft, iconColor);
            const iconEl = selectedIcon
              ? el('span', {
                  className: 'faq-arrow',
                  style: iconStyle,
                  dangerouslySetInnerHTML: { __html: applyIconSize(selectedIcon, 20) }
                })
              : el('span', { className: 'faq-arrow', style: iconStyle }, iconLeft ? '▶' : '◀');
            return el(
              'summary',
              { style: summaryStyle },
              iconLeft
                ? [iconEl, el(questionTag, { style: { fontSize: `${questionSize}px`, color: questionTextColor } }, faqItem.question)]
                : [el(questionTag, { style: { fontSize: `${questionSize}px`, color: questionTextColor } }, faqItem.question), iconEl]
            );
          };

          const detailsEls = faqs.map((faqItem, idx) => {
            let detailsStyle;
            if (designStyle === 'default') {
              detailsStyle = { marginBottom: '10px', borderBottom: '1px solid #ccc', paddingBottom: '10px' };
            } else if (designStyle === 'iconRight') {
              detailsStyle = { marginBottom: '10px' };
            } else if (designStyle === 'card') {
              detailsStyle = {
                marginBottom: '20px',
                border: '1px solid #ddd',
                borderRadius: '5px',
                boxShadow: '0 2px 5px rgba(0,0,0,0.1)',
                overflow: 'hidden'
              };
            }
            return el(
              'details',
              { key: idx, style: detailsStyle },
              renderSummaryStatic(faqItem),
              el(
                'div',
                { style: { padding: `${answerPaddingVertical}px ${answerPaddingHorizontal}px` } },
                el('p', {
                  style: { fontSize: `${answerSize}px`, color: answerTextColor },
                  dangerouslySetInnerHTML: { __html: faqItem.answer }
                })
              )
            );
          });

          const faqContainer = el(
            'div',
            {
              className: `asap-faq-accordion ${designStyle}`,
              style: { backgroundColor, padding: `${containerPadding}px` }
            },
            detailsEls
          );

          // Transiciones y rotación de íconos
          const transitionVal = useTransition ? 'all 0.15s ease' : 'none';
          const rotateValue = iconLeft ? '-90deg' : '90deg';
          let css = `.asap-faq-accordion details > div {
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transform: translateY(-10px);
      transition: ${transitionVal};
    }
    .asap-faq-accordion details[open] > div {
      max-height: 1000px;
      opacity: 1;
      transform: translateY(0);
    }`;      
          if (rotateIcons) {
            css += `.asap-faq-accordion details summary .faq-arrow { transition: none; }
    .asap-faq-accordion details[open] summary .faq-arrow { transform: rotate(${rotateValue}); }`;
          }
          const transitionStyles = el('style', {}, css);

          if (schemaData) {
            const schemaString = JSON.stringify(schemaData);
            return el(
              Fragment,
              {},
              faqContainer,
              transitionStyles,
              el('script', {
                type: 'application/ld+json',
                dangerouslySetInnerHTML: { __html: schemaString }
              })
            );
          }
          return el(Fragment, {}, faqContainer, transitionStyles);
        }
      });
    })(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);








    /** 
 * ======================================================
 *  BLOQUE: “ASAP − Social Locker” (con pop-up real)
 * ======================================================
 */
(function (blocks, element, editor, components, i18n, blockEditor) {
  const { __ } = i18n;
  const { registerBlockType } = blocks;
  const { Fragment, createElement, useState, useEffect } = element;
  const { InspectorControls, RichText, InnerBlocks } = editor;
  const { PanelBody, TextControl, CheckboxControl, RangeControl } = components;
  const { PanelColorSettings } = blockEditor;

  // Helper opcional para eliminar etiquetas <p> (si se requiere)
  const removeParagraphTags = (html) =>
    html ? html.replace(/<\/p>/gi, '<br><br>').replace(/<p[^>]*>/gi, '') : '';

  // Sub-componente ManagedTextControl utilizando hooks
  const ManagedTextControl = (props) => {
    const { value = '', label, help, style, placeholder, onChange } = props;
    const [text, setText] = useState(value);

    useEffect(() => {
      setText(value || '');
    }, [value]);

    return createElement(TextControl, {
      label,
      help,
      style,
      placeholder,
      value: text,
      onChange: (val) => setText(val),
      onBlur: () => {
        if (onChange) onChange(text);
      },
    });
  };

  // Definición de redes sociales disponibles (se agregan WhatsApp y Pinterest)
  const SOCIAL_NETWORKS = [
    {
      value: 'facebook',
      label: 'Facebook',
      color: '#3b5998',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-facebook" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" /></svg>',
    },
    {
      value: 'twitter',
      label: 'X',
      color: '#0f1419',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
    },
    {
      value: 'linkedin',
      label: 'LinkedIn',
      color: '#0077b5',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-linkedin" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="2" /><line x1="8" y1="12" x2="8" y2="16" /><line x1="8" y1="8" x2="8" y2="8.01" /><line x1="12" y1="16" x2="12" y2="12" /><path d="M16 16v-3a2 2 0 0 0 -4 0" /></svg>',
    },
    {
      value: 'whatsapp',
      label: 'WhatsApp',
      color: '#25D366',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"></path><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"></path></svg>',
    },
    {
      value: 'pinterest',
      label: 'Pinterest',
      color: '#BD081C',
      icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"><path d="M8 20l4 -9"></path><path d="M10.7 14c.437 1.263 1.43 2 2.55 2c2.071 0 3.75 -1.554 3.75 -4a5 5 0 1 0 -9.7 1.7"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path></svg>',
    },
  ];

  registerBlockType('asap-theme/social-locker', {
    title: __('ASAP − Desbloqueo social', 'asap-theme'),
    description: __('Oculta contenido hasta que el usuario comparta (se abre un pop-up real) y cierre la ventana de compartir.', 'asap-theme'),
    icon: 'lock',
    category: 'common',
    keywords: ['asap', 'locker', 'social', 'bloqueado', 'contenido'],

    attributes: {
      lockerTitle: {
        type: 'string',
        default: __('Este contenido está bloqueado', 'asap-theme'),
      },
      lockerDescription: {
        type: 'string',
        default: __('Para verlo, realiza la acción en el botón de compartir.', 'asap-theme'),
      },
      buttonText: {
        type: 'string',
        default: __('Compartir', 'asap-theme'),
      },
      shareUrl: {
        type: 'string',
        default: '',
      },
      backgroundColor: {
        type: 'string',
        default: '#f8f9fa',
      },
      textColor: {
        type: 'string',
        default: '#000000',
      },
      socialNetworks: {
        type: 'array',
        default: ['facebook', 'twitter'],
      },
      shareButtonPaddingVertical: {
        type: 'number',
        default: 10,
      },
      shareButtonPaddingHorizontal: {
        type: 'number',
        default: 20,
      },
      shareButtonBorderRadius: {
        type: 'number',
        default: 5,
      },
      uniqueId: {
        type: 'string',
        default: '',
      },
    },

    // EDITOR (Backend)
    edit: (props) => {
      const { attributes, setAttributes } = props;
      const {
        lockerTitle,
        lockerDescription,
        buttonText,
        shareUrl,
        backgroundColor,
        textColor,
        socialNetworks,
        shareButtonPaddingVertical: paddingV,
        shareButtonPaddingHorizontal: paddingH,
        shareButtonBorderRadius: borderRadius,
        uniqueId,
      } = attributes;

      // Asignar uniqueId si no existe
      if (!uniqueId) {
        setAttributes({ uniqueId: 'asap_share_' + Date.now() });
      }

      // Función genérica para actualizar atributos
      const updateAttr = (key) => (value) => setAttributes({ [key]: value });

      // Inspector Controls
      const inspector = createElement(
        InspectorControls,
        {},
        createElement(
          PanelBody,
          { title: __('Redes sociales', 'asap-theme'), initialOpen: true },
          SOCIAL_NETWORKS.map((network) =>
            createElement(CheckboxControl, {
              key: network.value,
              label: network.label,
              checked: socialNetworks.includes(network.value),
              onChange: (newVal) => {
                const newSocial = newVal
                  ? [...socialNetworks, network.value]
                  : socialNetworks.filter((item) => item !== network.value);
                setAttributes({ socialNetworks: newSocial });
              },
            })
          )
        ),
        createElement(
          PanelBody,
          { title: __('Estilos del botón', 'asap-theme'), initialOpen: false },
          createElement(RangeControl, {
            label: __('Margin interno vertical', 'asap-theme'),
            value: paddingV,
            onChange: updateAttr('shareButtonPaddingVertical'),
            min: 0,
            max: 50,
          }),
          createElement(RangeControl, {
            label: __('Margin interno horizontal', 'asap-theme'),
            value: paddingH,
            onChange: updateAttr('shareButtonPaddingHorizontal'),
            min: 0,
            max: 50,
          }),
          createElement(RangeControl, {
            label: __('Radio del borde', 'asap-theme'),
            value: borderRadius,
            onChange: updateAttr('shareButtonBorderRadius'),
            min: 0,
            max: 50,
          })
        ),
        createElement(
          PanelBody,
          { title: __('Otros ajustes', 'asap-theme'), initialOpen: false },
          createElement(ManagedTextControl, {
            label: __('Texto del botón', 'asap-theme'),
            value: buttonText,
            onChange: updateAttr('buttonText'),
          }),
          createElement(ManagedTextControl, {
            label: __('URL', 'asap-theme'),
            value: shareUrl,
            onChange: updateAttr('shareUrl'),
            help: __('Si se deja vacío, se tomará la URL actual del frontend.', 'asap-theme'),
          })
        ),
        createElement(PanelColorSettings, {
          title: __('Colores', 'asap-theme'),
          initialOpen: true,
          colorSettings: [
            {
              label: __('Color del fondo', 'asap-theme'),
              value: backgroundColor,
              onChange: updateAttr('backgroundColor'),
            },
            {
              label: __('Color del texto', 'asap-theme'),
              value: textColor,
              onChange: updateAttr('textColor'),
            },
          ],
        })
      );

      // Vista previa de botones de compartir (no interactivos)
      const shareButtonsPreview = socialNetworks.map((netValue) => {
        const netObj = SOCIAL_NETWORKS.find((n) => n.value === netValue);
        if (!netObj) return null;
        return createElement(
          'button',
          {
            key: netValue,
            type: 'button',
            disabled: true,
            style: {
              background: netObj.color,
              color: '#fff',
              padding: `${paddingV}px ${paddingH}px`,
              border: 'none',
              borderRadius: `${borderRadius}px`,
              cursor: 'not-allowed',
              marginRight: '5px',
            },
          },
          [
            createElement('span', { dangerouslySetInnerHTML: { __html: netObj.icon } }),
            ' ',
            buttonText,
          ]
        );
      });

      return createElement(
        Fragment,
        {},
        inspector,
        createElement(
          'div',
          {
            className: 'asap-social-locker-editor',
            style: {
              backgroundColor,
              color: textColor,
              padding: '20px',
              border: '2px dashed #ccc',
            },
          },
          createElement(
            'div',
            {
              style: {
                padding: '10px',
                borderRadius: '5px',
                border: '1px solid #ddd',
                marginBottom: '10px',
              },
            },
            createElement('strong', {}, __('Contenido restringido', 'asap-theme')),
            createElement(
              'p',
              {},
              __('Agrega aquí debajo aquellos bloques de contenido que deseas ocultar hasta ser desbloqueados.', 'asap-theme')
            ),
            createElement(InnerBlocks, {})
          ),
          createElement(
            'div',
            {
              className: 'asap-social-locker-overlay',
              style: {
                background: backgroundColor,
                padding: '20px',
                textAlign: 'center',
              },
            },
            createElement(RichText, {
              tagName: 'h4',
              style: { color: textColor },
              value: lockerTitle,
              onChange: updateAttr('lockerTitle'),
              placeholder: __('Escribe el título bloqueado...', 'asap-theme'),
            }),
            createElement(RichText, {
              tagName: 'p',
              style: { color: textColor },
              value: lockerDescription,
              onChange: updateAttr('lockerDescription'),
              placeholder: __('Escribe la descripción bloqueada...', 'asap-theme'),
            }),
            createElement('div', {}, shareButtonsPreview)
          )
        )
      );
    },

    // FRONTEND (Salida)
    save: (props) => {
      const { attributes } = props;
      const {
        lockerTitle,
        lockerDescription,
        buttonText,
        shareUrl,
        backgroundColor = '#f8f9fa',
        textColor = '#000000',
        socialNetworks,
        shareButtonPaddingVertical: paddingV,
        shareButtonPaddingHorizontal: paddingH,
        shareButtonBorderRadius: borderRadius,
        uniqueId,
      } = attributes;

      const lockerCSS = `
        .asap-social-locker-wrapper { }
        .asap-social-locked-content { display: none; }
        .asap-social-locker-wrapper.asap-social-locker-unlocked .asap-social-locked-content { display: block; }
        .asap-social-locker-overlay { display: block; background: ${backgroundColor}; padding: 20px; text-align: center; }
        .asap-social-locker-wrapper.asap-social-locker-unlocked .asap-social-locker-overlay { display: none; }
      `;

      const shareButtons = socialNetworks.map((netValue) => {
        const netObj = SOCIAL_NETWORKS.find((n) => n.value === netValue);
        if (!netObj) return null;
        return createElement(
          'button',
          {
            type: 'button',
            onClick:
              'shareAndUnlock_' + uniqueId + '("' + netValue + '");',
            style: {
              background: netObj.color,
              color: '#fff',
              padding: `${paddingV}px ${paddingH}px`,
              border: 'none',
              borderRadius: `${borderRadius}px`,
              cursor: 'pointer',
              marginRight: '5px',
            },
          },
          [
            createElement('span', { dangerouslySetInnerHTML: { __html: netObj.icon } }),
            ' ',
            buttonText,
          ]
        );
      });

      const scriptJS = `
        function shareAndUnlock_${uniqueId}(network) {
          var urlToShare = "${shareUrl ? shareUrl : ''}";
          if (!urlToShare) { urlToShare = window.location.href; }
          var shareLink = "";
          if (network === "facebook") {
            shareLink = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(urlToShare) + "&display=popup";
          } else if (network === "twitter") {
            shareLink = "https://twitter.com/intent/tweet?url=" + encodeURIComponent(urlToShare);
          } else if (network === "linkedin") {
            shareLink = "https://www.linkedin.com/shareArticle?mini=true&url=" + encodeURIComponent(urlToShare);
          } else if (network === "whatsapp") {
            shareLink = "https://api.whatsapp.com/send?text=" + encodeURIComponent(urlToShare);
          } else if (network === "pinterest") {
            shareLink = "https://pinterest.com/pin/create/button/?url=" + encodeURIComponent(urlToShare);
          } else {
            shareLink = "https://example.com";
          }
          var popW = 600, popH = 400, leftPos = (screen.width - popW) / 2, topPos = (screen.height - popH) / 2;
          var pop = window.open(shareLink, "ShareWindow", "width=" + popW + ",height=" + popH + ",left=" + leftPos + ",top=" + topPos);
          if (!pop) {
            alert("Por favor, habilita las ventanas emergentes (pop-ups).");
            return;
          }
          var openTime = Date.now();
          var fbTimeout = null;
          var poll = setInterval(function() {
            if (pop.closed) {
              if (Date.now() - openTime > 1000) {
                clearInterval(poll);
                if (fbTimeout) { clearTimeout(fbTimeout); }
                var wrapper = document.getElementById("asap-social-locker-wrapper-${uniqueId}");
                if (wrapper) { wrapper.classList.add("asap-social-locker-unlocked"); }
              }
            }
          }, 500);
          if (network === "facebook") {
            fbTimeout = setTimeout(function() {
              clearInterval(poll);
              var wrapper = document.getElementById("asap-social-locker-wrapper-${uniqueId}");
              if (wrapper) { wrapper.classList.add("asap-social-locker-unlocked"); }
            }, 30000);
          }
        }
      `;

      return createElement(
        Fragment,
        {},
        createElement('style', {}, lockerCSS),
        createElement(
          'div',
          {
            id: 'asap-social-locker-wrapper-' + uniqueId,
            className: 'asap-social-locker-wrapper',
          },
          createElement(
            'div',
            { className: 'asap-social-locked-content' },
            createElement(InnerBlocks.Content, {})
          ),
          createElement(
            'div',
            { className: 'asap-social-locker-overlay' },
            createElement('h4', { style: { color: textColor } }, lockerTitle),
            createElement('p', { style: { color: textColor }, dangerouslySetInnerHTML: { __html: lockerDescription } }),
            createElement('div', {}, shareButtons)
          )
        ),
        createElement('script', { dangerouslySetInnerHTML: { __html: scriptJS } })
      );
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);












/**
 * ======================================================
 * BLOQUE: “ASAP − Pricing Table”
 * ======================================================
 */
(function (
  blocks,
  element,
  editor,
  components,
  i18n,
  blockEditor
) {
  const { __ } = i18n;
  const { registerBlockType } = blocks;
  const { 
    Fragment, 
    createElement: el, 
    useState, 
    useMemo, 
    useEffect, 
    useRef 
  } = element;
  const { RichText, InspectorControls } = editor;
  const {
    PanelBody,
    RangeControl,
    SelectControl,
    Button,
    TextControl,
    ToggleControl,
  } = components;
  const { PanelColorSettings } = blockEditor;

  // ===============================
  // Constantes de estilos reutilizables
  // ===============================
  const ICON_PICKER_CONTAINER_STYLE = {
    border: "1px solid #ccc",
    padding: "6px",
    borderRadius: "4px",
    marginBottom: "8px",
  };

  const ICON_PICKER_ICONS_WRAPPER_STYLE = {
    display: "flex",
    flexWrap: "wrap",
    maxHeight: "160px",
    overflowY: "auto",
  };

  const ICON_PICKER_BUTTON_STYLE = {
    width: "60px",
    height: "60px",
    margin: "3px",
    border: "1px solid #ddd",
    borderRadius: "4px",
    background: "#fff",
    cursor: "pointer",
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "center",
  };

  const ICON_PICKER_ICON_NAME_STYLE = {
    fontSize: "10px",
    textAlign: "center",
  };

  // Tamaño por defecto para el ícono de característica.
  const DEFAULT_FEATURE_ICON_SIZE = 16;

  // ===============================
  // Función auxiliar para aplicar tamaño al ícono
  // ===============================
  const applyFeatureIconSize = (iconHTML, sizePx) => {
    if (!iconHTML) return "";
    const styleStr = `display:inline-block;width:${sizePx}px;height:${sizePx}px;line-height:${sizePx}px;font-size:${sizePx}px;`;
    return iconHTML.replace(/(<(i|span)(\s+[^>]+)?)(>)/i, `$1 style="${styleStr}"$4`);
  };

  // ===============================
  // Funciones helper para estilos de columnas (usadas en edit y save)
  // ===============================
  const getBaseColStyle = (
    type,
    { colBg, borderRadius, borderThickness, borderColor, modernBgColor1, modernBgColor2, colText }
  ) => {
    let styleObj = {
      position: "relative",
      textAlign: "center",
      padding: "20px",
      boxSizing: "border-box",
      color: colText,
    };
    if (type === "style1") {
      styleObj = {
        ...styleObj,
        background: colBg,
        boxShadow: "0 4px 6px -1px rgba(0,0,0,0.15), 0 2px 4px -1px rgba(0,0,0,0.1)",
        borderRadius: `${borderRadius}px`,
      };
    } else if (type === "style2") {
      styleObj = {
        ...styleObj,
        background: colBg,
        border: `${borderThickness || 1}px solid ${borderColor}`,
        borderRadius: `${borderRadius}px`,
      };
    } else if (type === "style3") {
      styleObj = {
        ...styleObj,
        background: `linear-gradient(135deg, ${modernBgColor1 || "#a8dadc"} 0%, ${modernBgColor2 || "#457b9d"} 100%)`,
        borderRadius: `${borderRadius}px`,
        boxShadow: "0 4px 6px rgba(0,0,0,0.1)",
      };
    }
    return styleObj;
  };

  const styleObjectToString = (styleObj) => {
    return Object.entries(styleObj)
      .map(([key, value]) => {
        const kebabKey = key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
        return `${kebabKey}:${value}`;
      })
      .join(";");
  };

  // ===============================
  // Componente auxiliar para que RichText pueda autoenfocarse
  // ===============================
  const FocusableRichText = (props) => {
    const richTextRef = useRef();
    useEffect(() => {
      if (props.autoFocus && richTextRef.current && typeof richTextRef.current.focus === "function") {
        richTextRef.current.focus();
      }
    }, [props.autoFocus]);
    return el(RichText, { ...props, ref: richTextRef });
  };

  // ===============================
  // Componente funcional IconPicker con hooks (useState, useMemo)
  // ===============================
  const IconPicker = (props) => {
    const { onSelect } = props;
    const [filter, setFilter] = useState("");
    const filteredIcons = useMemo(() => {
      return ALL_FA_ICONS.filter((ic) =>
        ic.name.indexOf(filter.toLowerCase()) !== -1
      );
    }, [filter]);

    return el(
      "div",
      { style: ICON_PICKER_CONTAINER_STYLE },
      el(TextControl, {
        label: __("Buscar ícono", "asap-theme"),
        value: filter,
        onChange: setFilter,
        style: { marginBottom: "6px" },
      }),
      el(
        "div",
        { style: ICON_PICKER_ICONS_WRAPPER_STYLE },
        filteredIcons.map((obj) =>
          el(
            "button",
            {
              key: obj.class,
              type: "button",
              onClick: () => onSelect(obj.class),
              style: ICON_PICKER_BUTTON_STYLE,
            },
            el("i", { className: obj.class, style: { fontSize: "20px", marginBottom: "4px" } }),
            el("span", { style: ICON_PICKER_ICON_NAME_STYLE }, obj.name)
          )
        )
      )
    );
  };

  // ===============================
  // Componente FeatureIconSelector
  // ===============================
  class FeatureIconSelector extends element.Component {
    constructor(props) {
      super(props);
      this.state = { showPicker: false };
    }

    togglePicker = () => {
      this.setState((prevState) => ({ showPicker: !prevState.showPicker }));
    };

    onSelectIcon = (iconClass) => {
      if (this.props.onSelect) {
        this.props.onSelect(iconClass ? `<i class="${iconClass}"></i>` : "");
      }
      this.setState({ showPicker: false });
    };

    render() {
      return el(
        "div",
        {},
        el(
          "div",
          { style: { marginBottom: "8px" } },
          el("span", {}, __("Icono por defecto para características: ", "asap-theme")),
          el("span", { dangerouslySetInnerHTML: { __html: this.props.value } })
        ),
        el(
          Button,
          {
            onClick: () => this.onSelectIcon(""),
            isDestructive: true,
            style: { marginBottom: "8px" },
          },
          __("Quitar icono", "asap-theme")
        ),
        el(
          Button,
          { onClick: this.togglePicker, isSecondary: true },
          this.state.showPicker ? __("Cerrar Selector", "asap-theme") : __("Seleccionar Icono", "asap-theme")
        ),
        this.state.showPicker &&
          el(
            "div",
            {
              style: {
                border: "1px solid #ccc",
                padding: "6px",
                borderRadius: "4px",
                marginTop: "8px",
                maxHeight: "200px",
                overflowY: "auto",
              },
            },
            // Se reutiliza el componente IconPicker
            el(IconPicker, { onSelect: this.onSelectIcon })
          )
      );
    }
  }

  // ===============================
  // Función que genera el HTML final (save)
  // ===============================
  const generatePricingTableHTML = (attrs) => {
    const {
      plans = [],
      typeOfDesign = "style1",
      columns = 3,
      colBg,
      borderRadius = 4,
      recommendedColor,
      planPriceFontSize = 32,
      planTitleTag = "h4",
      planTitleFontSize = 22,
      planSubtitleFontSize = 16,
      buttonPaddingVertical = 10,
      buttonPaddingHorizontal = 20,
      borderThickness,
      borderColor,
      modernBgColor1,
      modernBgColor2,
      featureIcon,
      colText,
      buttonFontSize,
      buttonBg = "#0073aa",
      buttonTextColor = "#ffffff"
    } = attrs;

    const containerStyle = "width:100%;";
    const colsWrap = `display: grid; grid-template-columns: repeat(${columns}, 1fr); gap:20px;`;

    let colsHtml = "";
    plans.forEach((pl) => {
      let ribbonHTML = "";
      if (pl.recommended) {
        const ribbonTxt = pl.ribbonText || __("POPULAR", "asap-theme");
        ribbonHTML = `<div style="position:absolute;top:0;right:0;background:${recommendedColor};color:#fff;font-weight:bold;padding:5px 10px;text-align:center;font-size:10px;">${ribbonTxt}</div>`;
      }
      const cSymbol = pl.currencySymbol || "$";
      const cPrice = pl.price || "0";
      const cPeriod = pl.period || "";
      const periodHTML = cPeriod ? `<span style="font-size:0.5em;">${cPeriod}</span>` : "";
      const priceHTML = `<div style="font-size:${planPriceFontSize}px;margin-bottom:10px;">${cSymbol}${cPrice}${periodHTML}</div>`;

      const validFeats = (pl.features || []).filter((feat) => {
        const featText = typeof feat === "object" ? feat.text : feat;
        return featText && featText.trim() !== "";
      });
      let featsHtml = "";
      if (validFeats.length > 0) {
        featsHtml = '<ul style="text-align:left;margin:0 auto;max-width:200px;padding:0;">';
        validFeats.forEach((feat) => {
          const featText = typeof feat === "object" ? feat.text : feat;
          let iconHTML =
            typeof feat === "object" && feat.icon && feat.icon.trim() !== ""
              ? feat.icon
              : featureIcon;
          if (iconHTML && iconHTML.trim() !== "") {
            iconHTML = applyFeatureIconSize(iconHTML, DEFAULT_FEATURE_ICON_SIZE);
          }
          featsHtml += `<li style="list-style:none;margin:5px 0;">${iconHTML ? iconHTML + " " : ""}${featText}</li>`;
        });
        featsHtml += "</ul>";
      }
      let ctaBtn = "";
      if (pl.buttonText) {
        let extraAttrs = "";
        if (pl.buttonNoFollow) extraAttrs += ' rel="nofollow"';
        if (pl.buttonTargetBlank) extraAttrs += " target=\"_blank\"";
        ctaBtn = `<a href="${pl.buttonUrl || "#"}"${extraAttrs} style="display:inline-block;margin-top:15px;padding:${buttonPaddingVertical}px ${buttonPaddingHorizontal}px;background:${buttonBg};color:${buttonTextColor};text-decoration:none;border-radius:${borderRadius}px;font-size:${buttonFontSize ||
          16}px;">${pl.buttonText}</a>`;

      }

      // Se utiliza el helper para generar el estilo base y luego se convierte a string.
      const baseColStyleObj = getBaseColStyle(typeOfDesign, {
        colBg,
        borderRadius,
        borderThickness,
        borderColor,
        modernBgColor1,
        modernBgColor2,
        colText,
      });
      const finalColStyle = styleObjectToString(baseColStyleObj);

      colsHtml += `<div class="asap-price-col" style="${finalColStyle}">
        ${ribbonHTML}
        <${planTitleTag} style="margin-bottom:5px;font-size:${planTitleFontSize}px;">${pl.title ||
        __("Plan", "asap-theme")}</${planTitleTag}>
        <div style="margin-bottom:15px;color:#777;font-size:${planSubtitleFontSize}px;">${pl.subtitle || ""}</div>
        ${priceHTML}
        ${featsHtml}
        ${ctaBtn}
      </div>`;
    });

    const mediaQuery =
      '<style>@media (max-width:768px){ .asap-pricing-table-wrap { grid-template-columns: 1fr !important; } }</style>';
    const tableHtml = `<div class="asap-pricing-table ${typeOfDesign}" style="${containerStyle}">
      <div class="asap-pricing-table-wrap" style="${colsWrap}">
        ${colsHtml}
      </div>
    </div>`;

    return mediaQuery + tableHtml;
  };

  // ===============================
  // Registro del Bloque “ASAP − Pricing Table”
  // ===============================
  registerBlockType("asap-theme/pricing-table", {
    title: __("ASAP − Tabla de precios", "asap-theme"),
    description: __("Muestra planes de precios con características, 3 diseños y plan recomendado.", "asap-theme"),
    icon: "cart",
    category: "common",
    keywords: ["asap", "pricing", "table", "prices"],
    attributes: {
      columns: { type: "number", default: 3 },
      plans: {
        type: "array",
        default: [
          {
            recommended: false,
            ribbonText: __("POPULAR", "asap-theme"),
            title: __("Standard", "asap-theme"),
            subtitle: __("Para freelancers", "asap-theme"),
            currencySymbol: "$",
            price: "15",
            period: "/mes",
            features: [
              { text: __("100 búsquedas", "asap-theme"), icon: "" },
              { text: __("50 backlinks", "asap-theme"), icon: "" },
            ],
            buttonText: __("Comprar ahora", "asap-theme"),
            buttonUrl: "#",
            buttonNoFollow: false,
            buttonTargetBlank: false,
          },
          {
            recommended: true,
            ribbonText: __("POPULAR", "asap-theme"),
            title: __("Booster", "asap-theme"),
            subtitle: __("Para agencias", "asap-theme"),
            currencySymbol: "$",
            price: "49",
            period: "/mes",
            features: [
              { text: __("500 búsquedas", "asap-theme"), icon: "" },
              { text: __("200 backlinks", "asap-theme"), icon: "" },
            ],
            buttonText: __("Comprar ahora", "asap-theme"),
            buttonUrl: "#",
            buttonNoFollow: false,
            buttonTargetBlank: false,
          },
        ],
      },
      typeOfDesign: { type: "string", default: "style1" },
      backgroundColor: { type: "string", default: "#f0f0f0" },
      textColor: { type: "string", default: "#000000" },
      recommendedColor: { type: "string", default: "#e53935" },
      colBg: { type: "string", default: "#ffffff" },
      colText: { type: "string", default: "#333333" },
      borderColor: { type: "string", default: "#ccc" },
      borderThickness: { type: "number", default: 1 },
      planTitleTag: { type: "string", default: "h4" },
      planTitleFontSize: { type: "number", default: 22 },
      planPriceFontSize: { type: "number", default: 32 },
      planSubtitleFontSize: { type: "number", default: 16 },
      featureIcon: { type: "string", default: "✓" },
      buttonFontSize: { type: "number", default: 16 },
      buttonPaddingVertical: { type: "number", default: 10 },
      buttonPaddingHorizontal: { type: "number", default: 20 },
      modernBgColor1: { type: "string", default: "#a8dadc" },
      modernBgColor2: { type: "string", default: "#457b9d" },
      borderRadius: { type: "number", default: 4 },
      buttonBg: { type: "string", default: "#0073aa" },
      buttonTextColor: { type: "string", default: "#ffffff" },
    },
    edit: (props) => {
      const { attributes, setAttributes } = props;
      const {
        plans = [],
        columns,
        typeOfDesign,
        backgroundColor,
        textColor,
        recommendedColor,
        colBg,
        colText,
        borderColor,
        borderThickness,
        borderRadius,
        planTitleTag,
        planTitleFontSize,
        planPriceFontSize,
        planSubtitleFontSize,
        featureIcon,
        buttonFontSize,
        buttonPaddingVertical,
        buttonPaddingHorizontal,
        modernBgColor1,
        modernBgColor2,
        buttonBg,
        buttonTextColor,
      } = attributes;

      const updatePlan = (index, newData) => {
        const newPlans = [...plans];
        newPlans[index] = { ...newPlans[index], ...newData };
        setAttributes({ plans: newPlans });
      };

      const addPlan = () => {
        const newPlans = [...plans];
        newPlans.push({
          recommended: false,
          ribbonText: __("POPULAR", "asap-theme"),
          title: __("Nuevo Plan", "asap-theme"),
          subtitle: "",
          currencySymbol: "$",
          price: "99",
          period: "/month",
          features: [{ text: "", icon: "" }],
          buttonText: __("Comprar", "asap-theme"),
          buttonUrl: "",
          buttonNoFollow: false,
          buttonTargetBlank: false,
        });
        setAttributes({ plans: newPlans });
      };

      const removePlan = (index) => {
        const newPlans = [...plans];
        newPlans.splice(index, 1);
        setAttributes({ plans: newPlans });
      };

      const containerStyle = { width: "100%" };
      const colsWrapStyle = {
        display: "grid",
        gridTemplateColumns: `repeat(${columns}, 1fr)`,
        gap: "20px",
      };

      const planElements = plans.map((pl, i) => {
        const baseColStyle = getBaseColStyle(typeOfDesign, {
          colBg,
          borderRadius,
          borderThickness,
          borderColor,
          modernBgColor1,
          modernBgColor2,
          colText,
        });

        const ribbonToggle = el(ToggleControl, {
          label: __("Mostrar ribbon", "asap-theme"),
          checked: !!pl.recommended,
          onChange: (val) => updatePlan(i, { recommended: val }),
          style: { textAlign: "left" },
        });
        const ribbonTextControl = pl.recommended
          ? el(RichText, {
              tagName: "div",
              value: pl.ribbonText,
              onChange: (val) => updatePlan(i, { ribbonText: val }),
              placeholder: __("Texto del Ribbon", "asap-theme"),
              style: {
                position: "absolute",
                top: 0,
                right: 0,
                background: recommendedColor,
                color: "#fff",
                fontWeight: "bold",
                padding: "5px 10px",
                textAlign: "center",
              },
            })
          : null;
        const ribbonControls = el(
          "div",
          { style: { marginBottom: "10px", textAlign: "left" } },
          ribbonToggle,
          ribbonTextControl
        );

        const titleEl = el(RichText, {
          tagName: planTitleTag,
          value: pl.title,
          onChange: (val) => updatePlan(i, { title: val }),
          placeholder: __("Título", "asap-theme"),
          style: { marginBottom: "5px", fontSize: `${planTitleFontSize}px` },
        });
        const subtitleEl = el(RichText, {
          tagName: "div",
          value: pl.subtitle,
          onChange: (val) => updatePlan(i, { subtitle: val }),
          placeholder: __("Subtítulo", "asap-theme"),
          style: { marginBottom: "15px", color: "#777", fontSize: `${planSubtitleFontSize}px` },
        });
        const priceEl = el(
          "div",
          { style: { fontSize: `${planPriceFontSize}px`, marginBottom: "10px" } },
          el(RichText, {
            tagName: "span",
            value: pl.currencySymbol,
            onChange: (val) => updatePlan(i, { currencySymbol: val }),
            placeholder: __("$", "asap-theme"),
            style: { marginRight: "2px" },
          }),
          el(RichText, {
            tagName: "span",
            value: pl.price,
            onChange: (val) => updatePlan(i, { price: val }),
            placeholder: __("Precio", "asap-theme"),
          }),
          el(RichText, {
            tagName: "span",
            value: pl.period,
            onChange: (val) => updatePlan(i, { period: val }),
            placeholder: __("Período", "asap-theme"),
            style: { fontSize: "0.5em", marginLeft: "2px" },
          })
        );
        const featuresEl =
          pl.features && pl.features.length > 0
            ? el(
                "ul",
                { style: { textAlign: "left", margin: "0 auto", maxWidth: "200px", padding: 0 } },
                pl.features.map((feat, j) =>
                  el(
                    "li",
                    { key: j, style: { listStyle: "none", margin: "5px 0" } },
                    el("span", {
                      dangerouslySetInnerHTML: {
                        __html: applyFeatureIconSize(
                          typeof feat === "object" && feat.icon && feat.icon.trim() !== ""
                            ? feat.icon
                            : featureIcon,
                          DEFAULT_FEATURE_ICON_SIZE
                        ),
                      },
                      style: { marginRight: "5px" },
                    }),
                    el(FocusableRichText, {
                      tagName: "span",
                      value: typeof feat === "object" ? feat.text : feat,
                      onChange: (val) => {
                        const newFeatures = [...pl.features];
                        // Se actualiza la característica sin modificar (o asignar) la propiedad autoFocus
                        newFeatures[j] = { text: val, icon: feat.icon };
                        updatePlan(i, { features: newFeatures });
                      },
                      onKeyDown: (event) => {
                        if (event.key === "Enter") {
                          event.preventDefault();
                          const newFeatures = [...pl.features];
                          newFeatures.splice(j + 1, 0, { text: "", icon: "", autoFocus: true });
                          updatePlan(i, { features: newFeatures });
                        } else if (event.key === "Backspace") {
                          const currentText = typeof feat === "object" ? feat.text : feat;
                          if (currentText.trim() === "") {
                            event.preventDefault();
                            const newFeatures = [...pl.features];
                            newFeatures.splice(j, 1);
                            if (j > 0) {
                              newFeatures[j - 1].autoFocus = true;
                            }
                            updatePlan(i, { features: newFeatures });
                          }
                        }
                      },
                      placeholder: __("Característica", "asap-theme"),
                      autoFocus: !!feat.autoFocus,
                    })
                  )
                )
              )
            : null;
        const ctaEl = el(RichText, {
          tagName: "a",
          value: pl.buttonText,
          onChange: (val) => updatePlan(i, { buttonText: val }),
          placeholder: __("Texto del botón", "asap-theme"),
          style: {
            display: "inline-block",
            marginTop: "15px",
            padding: `${buttonPaddingVertical}px ${buttonPaddingHorizontal}px`,
            background: buttonBg,
            color: buttonTextColor,
            textDecoration: "none",
            borderRadius: `${borderRadius}px`,
            fontSize: `${buttonFontSize}px`,
            marginBottom: "20px",
          },
        });
        const buttonUrlControl = el(TextControl, {
          value: pl.buttonUrl,
          onChange: (val) => updatePlan(i, { buttonUrl: val }),
          label: __("Enlace CTA", "asap-theme"),
          placeholder: __("URL de destino", "asap-theme"),
          style: { marginTop: "5px", marginBottom: "10px" },
        });
        const noFollowControl = el(ToggleControl, {
          label: __("Agregar atributo nofollow", "asap-theme"),
          checked: !!pl.buttonNoFollow,
          onChange: (val) => updatePlan(i, { buttonNoFollow: val }),
          style: { textAlign: "left" },
        });
        const targetBlankControl = el(ToggleControl, {
          label: __("Abrir en nueva pestaña", "asap-theme"),
          checked: !!pl.buttonTargetBlank,
          onChange: (val) => updatePlan(i, { buttonTargetBlank: val }),
          style: { textAlign: "left" },
        });
        const removePlanButton = el(
          Button,
          {
            isDestructive: true,
            onClick: () => removePlan(i),
            style: { marginTop: "10px" },
          },
          __("Eliminar plan", "asap-theme")
        );

        return el(
          "div",
          { key: i, className: "asap-price-col", style: baseColStyle },
          ribbonControls,
          titleEl,
          subtitleEl,
          priceEl,
          featuresEl,
          ctaEl,
          buttonUrlControl,
          noFollowControl,
          targetBlankControl,
          removePlanButton
        );
      });

      const featureIconPanel = el(
        PanelBody,
        { title: __("Características", "asap-theme"), initialOpen: false },
        el(FeatureIconSelector, {
          value: featureIcon,
          onSelect: (newIcon) => setAttributes({ featureIcon: newIcon }),
        })
      );

      const designPanel = el(
        PanelBody,
        { title: __("Diseño general", "asap-theme"), initialOpen: true },
        el(RangeControl, {
          label: __("Columnas (desktop)", "asap-theme"),
          value: columns,
          onChange: (val) => setAttributes({ columns: val }),
          min: 1,
          max: 6,
        }),
        el(SelectControl, {
          label: __("Tipo de Diseño", "asap-theme"),
          value: typeOfDesign,
          options: [
            { label: __("Sombra Avanzada", "asap-theme"), value: "style1" },
            { label: __("Bordes Personalizados", "asap-theme"), value: "style2" },
            { label: __("Fondo Moderno", "asap-theme"), value: "style3" },
          ],
          onChange: (val) => setAttributes({ typeOfDesign: val }),
        }),
        typeOfDesign === "style2" &&
          el(RangeControl, {
            label: __("Grosor del borde", "asap-theme"),
            value: borderThickness,
            onChange: (val) => setAttributes({ borderThickness: val }),
            min: 0,
            max: 10,
          })
      );

      const sizePanel = el(
        PanelBody,
        { title: __("Tamaños", "asap-theme"), initialOpen: false },
        el(RangeControl, {
          label: __("Tamaño Título", "asap-theme"),
          value: planTitleFontSize,
          onChange: (val) => setAttributes({ planTitleFontSize: val }),
          min: 10,
          max: 100,
        }),
        el(RangeControl, {
          label: __("Tamaño Precio", "asap-theme"),
          value: planPriceFontSize,
          onChange: (val) => setAttributes({ planPriceFontSize: val }),
          min: 10,
          max: 100,
        }),
        el(RangeControl, {
          label: __("Tamaño Bajada", "asap-theme"),
          value: planSubtitleFontSize,
          onChange: (val) => setAttributes({ planSubtitleFontSize: val }),
          min: 10,
          max: 100,
        }),
        el(RangeControl, {
          label: __("Tamaño botón", "asap-theme"),
          value: buttonFontSize,
          onChange: (val) => setAttributes({ buttonFontSize: val }),
          min: 8,
          max: 100,
        }),
        el(RangeControl, {
          label: __("Padding vertical botón", "asap-theme"),
          value: buttonPaddingVertical,
          onChange: (val) => setAttributes({ buttonPaddingVertical: val }),
          min: 0,
          max: 50,
        }),
        el(RangeControl, {
          label: __("Padding horizontal botón", "asap-theme"),
          value: buttonPaddingHorizontal,
          onChange: (val) => setAttributes({ buttonPaddingHorizontal: val }),
          min: 0,
          max: 50,
        }),
        el(RangeControl, {
          label: __("Border Radius", "asap-theme"),
          value: borderRadius,
          onChange: (val) => setAttributes({ borderRadius: val }),
          min: 0,
          max: 50,
        })
      );

      const inspector = el(
        InspectorControls,
        {},
        designPanel,
        PanelColorSettings &&
          el(PanelColorSettings, {
            title: __("Colores", "asap-theme"),
            initialOpen: false,
            colorSettings: [
              {
                value: textColor,
                onChange: (val) => setAttributes({ textColor: val }),
                label: __("Color texto sección", "asap-theme"),
              },
              {
                value: colBg,
                onChange: (val) => setAttributes({ colBg: val }),
                label: __("Color fondo plan", "asap-theme"),
              },
              {
                value: colText,
                onChange: (val) => setAttributes({ colText: val }),
                label: __("Color texto plan", "asap-theme"),
              },
              {
                value: borderColor,
                onChange: (val) => setAttributes({ borderColor: val }),
                label: __("Color borde plan", "asap-theme"),
              },
              {
                value: recommendedColor,
                onChange: (val) => setAttributes({ recommendedColor: val }),
                label: __("Color recomendado", "asap-theme"),
              },
              {
                value: buttonBg,
                onChange: val => setAttributes({ buttonBg: val }),
                label: __("Color fondo botón","asap-theme")
              },
              {
                value: buttonTextColor,
                onChange: val => setAttributes({ buttonTextColor: val }),
                label: __("Color texto botón","asap-theme")
              },
            ],
          }),
        typeOfDesign === "style3" &&
          el(PanelColorSettings, {
            title: __("Fondo Moderno", "asap-theme"),
            initialOpen: false,
            colorSettings: [
              {
                value: modernBgColor1,
                onChange: (val) => setAttributes({ modernBgColor1: val }),
                label: __("Color Base 1", "asap-theme"),
              },
              {
                value: modernBgColor2,
                onChange: (val) => setAttributes({ modernBgColor2: val }),
                label: __("Color Base 2", "asap-theme"),
              },
            ],
          }),
        sizePanel,
        featureIconPanel
      );

      const interactivePreview = el(
        "div",
        { className: "asap-pricing-table", style: containerStyle },
        el("div", { className: "asap-pricing-table-wrap", style: colsWrapStyle }, planElements)
      );

      return el(
        Fragment,
        {},
        inspector,
        interactivePreview,
        el(
          Button,
          {
            isPrimary: true,
            style: { marginTop: "2rem" },
            onClick: addPlan,
          },
          __("+ Añadir plan", "asap-theme")
        )
      );
    },
    save: (props) => {
      const { attributes } = props;
      return el(
        Fragment,
        {},
        el("div", { dangerouslySetInnerHTML: { __html: generatePricingTableHTML(attributes) } })
      );
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);











    /* =========================================================
       BLOQUE: “ASAP − Collapsable Excerpt” (slide desde el excerpt)
    ========================================================== */
    (function (blocks, element, editor, components, i18n, blockEditor) {
      const { __ } = i18n;
      const { registerBlockType } = blocks;
      const { Fragment, createElement, useState, useEffect } = element;
      const { InspectorControls, RichText } = editor;
      const { PanelBody, RangeControl, ToggleControl, TextControl } = components;
      const { PanelColorSettings } = blockEditor;

      // Convierte saltos de línea en párrafos, aplicando inline style si se pasa textColor.
      const toParagraphs = (str, textColor) => {
        if (!str) return '';
        const cleanStr = str.replace(/<br\s*[\/]?>/gi, '\n');
        const lines = cleanStr
          .split(/\r?\n+/)
          .map(line => line.trim())
          .filter(line => line.length > 0);
        return lines
          .map(line =>
            textColor
              ? `<p style="color:${textColor};">${line}</p>`
              : `<p>${line}</p>`
          )
          .join('');
      };

      // Separa el texto en dos partes (excerpt y extra) según el límite de palabras.
      const getExcerptAndExtra = (text, wordLimit) => {
        const lines = text.split(/\r?\n/);
        let wordsCount = 0;
        const excerptLines = [];
        const extraLines = [];
        let reachedLimit = false;
        for (const line of lines) {
          const wordsInLine = line.split(/\s+/).filter(word => word.length > 0);
          if (!reachedLimit) {
            if (wordsCount + wordsInLine.length <= wordLimit) {
              excerptLines.push(line);
              wordsCount += wordsInLine.length;
            } else {
              const remaining = wordLimit - wordsCount;
              const wordsArr = line.split(/\s+/);
              const excerptPart = wordsArr.slice(0, remaining).join(' ');
              const extraPart = wordsArr.slice(remaining).join(' ');
              excerptLines.push(excerptPart);
              extraLines.push(extraPart);
              reachedLimit = true;
            }
          } else {
            extraLines.push(line);
          }
        }
        return {
          excerpt: excerptLines.join("\n"),
          extra: extraLines.join("\n")
        };
      };

      // ManagedRichText: Componente funcional que actualiza onBlur para evitar “lag”.
      const ManagedRichText = (props) => {
        const { value = '', tagName, placeholder, style, formattingControls = [], multiline = false, onChange } = props;
        const [text, setText] = useState(value);
        useEffect(() => { setText(value || ''); }, [value]);
        return createElement(RichText, {
          tagName,
          value: text,
          placeholder,
          style,
          formattingControls,
          multiline,
          onChange: val => setText(val),
          onBlur: () => { if (onChange) onChange(text); }
        });
      };

      // ManagedTextControl: Componente funcional que actualiza onBlur.
      const ManagedTextControl = (props) => {
        const { value = '', label, placeholder, style, help, onChange } = props;
        const [text, setText] = useState(value);
        useEffect(() => { setText(value || ''); }, [value]);
        return createElement(TextControl, {
          label,
          placeholder,
          style,
          help,
          value: text,
          onChange: val => setText(val),
          onBlur: () => { if (onChange) onChange(text); }
        });
      };

      // Genera el CSS para el overlay de fade usando el checkbox hack.
      const generateFadeOverlayCSS = (uniqueId, fadeColor) => `
        #${uniqueId} .asap-ce-extra-container {
          overflow: hidden;
          max-height: 0;
          transition: max-height 0.5s ease;
        }
        #${uniqueId} input.asap-ce-toggle:checked ~ .asap-ce-extra-container {
          max-height: 1000px;
        }
        #${uniqueId} .asap-ce-fade {
          position: absolute;
          left: 0;
          right: 0;
          bottom: 0;
          height: 60px;
          background: linear-gradient(to bottom, rgba(255,255,255,0), ${fadeColor});
        }
        #${uniqueId} input.asap-ce-toggle:checked ~ .asap-ce-excerpt .asap-ce-fade {
          display: none;
        }
        #${uniqueId} .show-less { display: none; }
        #${uniqueId} input.asap-ce-toggle:checked ~ .show-more { display: none; }
        #${uniqueId} input.asap-ce-toggle:checked ~ .show-less { display: inline-block; }
      `;

      // Retorna el estilo del contenedor para la salida (save).
      const getContainerStyle = ({ backgroundColor, textColor, containerPadding }) => ({
        background: backgroundColor,
        color: textColor,
        position: 'relative',
        padding: `${containerPadding}px`,
        marginBottom: '2rem'
      });

      // Retorna el estilo del botón "mostrar más/menos".
      const getButtonStyle = ({ buttonColor, buttonTextColor }) => ({
        marginTop: '10px',
        background: buttonColor,
        color: buttonTextColor,
        padding: '8px 16px',
        border: 'none',
        borderRadius: '4px',
        cursor: 'pointer'
      });

      registerBlockType('asap-theme/collapsable-excerpt', {
        title: __('ASAP − Mensaje colapsable', 'asap-theme'),
        description: __('Corta tras N palabras y permite expandir el contenido con efecto slide desde el punto donde quedó el excerpt.', 'asap-theme'),
        icon: 'editor-contract',
        category: 'common',
        keywords: ['asap', 'collapsable', 'excerpt', 'mostrar más', 'slide'],

        attributes: {
          content: {
            type: 'string',
            default: __('Introduce aquí un texto largo que quieras que sea colapsable/expandible.', 'asap-theme')
          },
          wordLimit: { type: 'number', default: 50 },
          showMoreText: { type: 'string', default: __('Mostrar más', 'asap-theme') },
          showLessText: { type: 'string', default: __('Mostrar menos', 'asap-theme') },
          fadeColor: { type: 'string', default: '#ffffff' },
          backgroundColor: { type: 'string', default: '#ffffff' },
          textColor: { type: 'string', default: '#000000' },
          buttonColor: { type: 'string', default: '#0073aa' },
          buttonTextColor: { type: 'string', default: '#ffffff' },
          startOpen: { type: 'boolean', default: false },
          containerPadding: { type: 'number', default: 0 },
          uniqueId: { type: 'string', default: '' }
        },

        // ===== EDITOR =====
        edit: (props) => {
          const { attributes, setAttributes } = props;
          const {
            content = '',
            wordLimit,
            showMoreText,
            showLessText,
            fadeColor,
            backgroundColor,
            textColor,
            buttonColor,
            buttonTextColor,
            startOpen,
            containerPadding,
            uniqueId
          } = attributes;

          if (!uniqueId) {
            setAttributes({ uniqueId: 'asap-ce-wrap-' + Date.now() });
          }

          // Función genérica para actualizar atributos.
          const updateAttr = key => value => setAttributes({ [key]: value });
          const onChangeWordLimit = value => setAttributes({ wordLimit: parseInt(value, 10) || 50 });

          const inspector = createElement(
            InspectorControls,
            {},
            createElement(
              PanelBody,
              { title: __('Ajustes Generales', 'asap-theme'), initialOpen: true },
              createElement(RangeControl, {
                label: __('Cantidad de Palabras', 'asap-theme'),
                value: wordLimit,
                onChange: onChangeWordLimit,
                min: 10,
                max: 1000
              }),
              createElement(ToggleControl, {
                label: __('¿Iniciar expandido?', 'asap-theme'),
                checked: startOpen,
                onChange: updateAttr('startOpen')
              }),
              createElement(ManagedTextControl, {
                label: __('Texto Mostrar más', 'asap-theme'),
                value: showMoreText,
                onChange: updateAttr('showMoreText')
              }),
              createElement(ManagedTextControl, {
                label: __('Texto Mostrar menos', 'asap-theme'),
                value: showLessText,
                onChange: updateAttr('showLessText')
              }),
              createElement(RangeControl, {
                label: __('Margen Interno', 'asap-theme'),
                value: containerPadding,
                onChange: updateAttr('containerPadding'),
                min: 0,
                max: 50
              })
            ),
            createElement(
              PanelColorSettings,
              {
                title: __('Colores', 'asap-theme'),
                initialOpen: false,
                colorSettings: [
                  { value: backgroundColor, onChange: updateAttr('backgroundColor'), label: __('Fondo') },
                  { value: textColor, onChange: updateAttr('textColor'), label: __('Texto') },
                  { value: fadeColor, onChange: updateAttr('fadeColor'), label: __('Fade') },
                  { value: buttonColor, onChange: updateAttr('buttonColor'), label: __('Fondo del botón') },
                  { value: buttonTextColor, onChange: updateAttr('buttonTextColor'), label: __('Texto del botón') }
                ]
              }
            )
          );

          const containerStyle = {
            backgroundColor,
            color: textColor,
            padding: `${containerPadding}px`,
            border: '1px dashed #ddd'
          };

          return createElement(
            Fragment,
            {},
            inspector,
            createElement(
              'div',
              { className: 'asap-collapsable-excerpt-editor', style: containerStyle },
              createElement(ManagedRichText, {
                tagName: 'div',
                value: content,
                onChange: updateAttr('content'),
                placeholder: __('Escribe texto largo…', 'asap-theme'),
                style: { minHeight: '80px' }
              })
            )
          );
        },

        // ===== SAVE =====
        save: (props) => {
          const { attributes } = props;
          const {
            content = '',
            wordLimit = 50,
            showMoreText = __('Mostrar más', 'asap-theme'),
            showLessText = __('Mostrar menos', 'asap-theme'),
            fadeColor = '#ffffff',
            backgroundColor = '#ffffff',
            textColor = '#000000',
            buttonColor = '#0073aa',
            buttonTextColor = '#ffffff',
            startOpen,
            containerPadding = 0,
            uniqueId
          } = attributes;

          const rawText = content.trim();
          const words = rawText.split(/\s+/).filter(word => word.length > 0);
          const needsCollapse = words.length > wordLimit;

          if (!needsCollapse) {
            const finalAll = toParagraphs(rawText, textColor);
            return createElement(
              Fragment,
              {},
              createElement('div', {
                className: 'asap-ce-no-collapse',
                style: { background: backgroundColor, padding: `${containerPadding}px` },
                dangerouslySetInnerHTML: { __html: finalAll }
              })
            );
          }

          const parts = getExcerptAndExtra(rawText, wordLimit);
          const excerptHTML = toParagraphs(parts.excerpt, textColor);
          const extraHTML = toParagraphs(parts.extra, textColor);
          const fadeOverlayCSS = generateFadeOverlayCSS(uniqueId, fadeColor);
          const containerStyle = getContainerStyle({ backgroundColor, textColor, containerPadding });
          const btnStyle = getButtonStyle({ buttonColor, buttonTextColor });

          return createElement(
            Fragment,
            {},
            createElement('style', {}, fadeOverlayCSS),
            createElement(
              'div',
              { id: uniqueId, className: 'asap-ce-wrapper', style: containerStyle },
              createElement('input', {
                type: 'checkbox',
                className: 'asap-ce-toggle',
                id: uniqueId + '-toggle',
                style: { display: 'none' },
                defaultChecked: startOpen
              }),
              createElement(
                'div',
                { className: 'asap-ce-excerpt', style: { position: 'relative' } },
                createElement('div', { dangerouslySetInnerHTML: { __html: excerptHTML } }),
                createElement('div', { className: 'asap-ce-fade' })
              ),
              createElement(
                'div',
                { className: 'asap-ce-extra-container' },
                createElement('div', { className: 'asap-ce-extra', dangerouslySetInnerHTML: { __html: extraHTML } })
              ),
              createElement(
                'label',
                {
                  htmlFor: uniqueId + '-toggle',
                  className: 'asap-ce-btn show-more',
                  style: btnStyle
                },
                showMoreText
              ),
              createElement(
                'label',
                {
                  htmlFor: uniqueId + '-toggle',
                  className: 'asap-ce-btn show-less',
                  style: btnStyle
                },
                showLessText
              )
            )
          );
        }
      });
    })(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);

/* =========================================
   BLOQUE: “ASAP − Hero (Full)”
========================================= */
(function(blocks, element, editor, components, i18n, blockEditor) {
  const { __ } = i18n;
  const { registerBlockType } = blocks;
  const { Fragment, createElement, useState, useEffect } = element;
  const { InspectorControls, MediaUpload, RichText } = editor;
  const {
    PanelBody,
    SelectControl,
    RangeControl,
    ColorPalette,
    ToggleControl,
    TextControl,
    Button
  } = components;
  const { PanelColorSettings, InnerBlocks } = blockEditor;

  // --------------------------------------------------------------------
  // HELPERS
  // --------------------------------------------------------------------

  const removeParagraphTags = (html) => {
    if (!html) return '';
    return html
      .replace(/<\/p>/gi, '<br><br>')
      .replace(/<p[^>]*>/gi, '');
  };

  const parseHexColorToRGB = (hex) => {
    if (!hex || hex[0] !== '#') {
      return { r: 0, g: 0, b: 0 };
    }
    let cleanHex = hex.replace('#', '');
    if (cleanHex.length === 3) {
      cleanHex = cleanHex[0] + cleanHex[0] +
                 cleanHex[1] + cleanHex[1] +
                 cleanHex[2] + cleanHex[2];
    }
    const r = parseInt(cleanHex.substring(0, 2), 16);
    const g = parseInt(cleanHex.substring(2, 4), 16);
    const b = parseInt(cleanHex.substring(4, 6), 16);
    return { r, g, b };
  };

  const rgbaColor = (hex, opacity) => {
    const { r, g, b } = parseHexColorToRGB(hex);
    return `rgba(${r},${g},${b},${opacity})`;
  };

  const getOverlayDivStyle = (overlayEnabled, overlayColor, overlayOpacity) => {
    return overlayEnabled
      ? {
          position: 'absolute',
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          backgroundColor: rgbaColor(overlayColor, overlayOpacity),
        }
      : {};
  };

  const getContainerStyle = (
    isSidebarOpen,
    textColor,
    heroHeight,
    backgroundType,
    backgroundColor,
    backgroundImage,
    backgroundImagePosition
  ) => {
    const baseStyle = {
      width: isSidebarOpen ? '100%' : 'calc(100vw - 32px)',
      marginLeft: isSidebarOpen ? 0 : 'calc(-50vw + 50% + 16px)',
      position: 'relative',
      color: textColor,
      textAlign: 'center',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      overflowX: 'hidden',
      marginBottom: '26px',
    };
    if (heroHeight === '100vh') {
      baseStyle.height = '100vh';
    } else if (heroHeight === 'auto') {
      baseStyle.height = 'auto';
    } else {
      baseStyle.height = heroHeight + 'px';
    }
    if (backgroundType === 'color') {
      baseStyle.backgroundColor = backgroundColor;
    } else if (backgroundType === 'image' && backgroundImage) {
      baseStyle.backgroundImage = `url(${backgroundImage})`;
      baseStyle.backgroundSize = 'cover';
      baseStyle.backgroundPosition = backgroundImagePosition;
    } else if (backgroundType === 'image' && !backgroundImage) {
      baseStyle.backgroundColor = backgroundColor;
    } else if (backgroundType === 'video') {
      baseStyle.backgroundColor = backgroundColor;
    }
    return baseStyle;
  };

  const getCTAButtonStyle = (
    ctaBg,
    ctaColor,
    ctaPaddingVertical,
    ctaPaddingHorizontal
  ) => ({
    display: 'inline-block',
    padding: `${ctaPaddingVertical}px ${ctaPaddingHorizontal}px`,
    borderRadius: '4px',
    textDecoration: 'none',
    background: ctaBg,
    color: ctaColor,
    marginBottom: '20px',
    marginTop: '10px',
  });

  const getCTAHoverStyle = (
    ctaHoverEffect,
    ctaHoverColor,
    ctaHoverTextColor
  ) => {
    if (ctaHoverEffect === 'color') {
      return `.asap-herob-block-full .cta-button:hover { background: ${ctaHoverColor} !important; color: ${ctaHoverTextColor} !important; transition: background 0.3s ease, color 0.3s ease; }`;
    } else if (ctaHoverEffect === 'gradient') {
      return (
        `.asap-herob-block-full .cta-button { position: relative; overflow: hidden; }` +
        `.asap-herob-block-full .cta-button::after { content: ""; position: absolute; top: 0; left: -75%; width: 50%; height: 100%; background: linear-gradient(120deg, transparent, rgba(255,255,255,0.4), transparent); transform: skewX(-25deg); opacity: 0; }` +
        `.asap-herob-block-full .cta-button:hover::after { animation: shine 0.8s; }` +
        `@keyframes shine { 0% { left: -75%; opacity: 0; } 50% { left: 125%; opacity: 1; } 100% { left: 125%; opacity: 0; } }`
      );
    }
    return '';
  };

  // --------------------------------------------------------------------
  // SUBCOMPONENTES
  // --------------------------------------------------------------------

  const ManagedRichText = (props) => {
    const {
      value = '',
      tagName,
      placeholder,
      style,
      formattingControls = [],
      multiline = false,
      onChange,
    } = props;
    const [text, setText] = useState(value);
    useEffect(() => { setText(value || ''); }, [value]);
    return createElement(RichText, {
      tagName,
      value: text,
      placeholder,
      style,
      formattingControls,
      multiline,
      onChange: (val) => setText(val),
      onBlur: () => { if (onChange) onChange(text); },
    });
  };

  const ManagedTextControl = (props) => {
    const { value = '', label, placeholder, style, help, onChange } = props;
    const [text, setText] = useState(value);
    useEffect(() => { setText(value || ''); }, [value]);
    return createElement(TextControl, {
      label,
      placeholder,
      style,
      help,
      value: text,
      onChange: (val) => setText(val),
      onBlur: () => { if (onChange) onChange(text); },
    });
  };

  const CTAButtonEdit = (props) => {
    const {
      ctaBg,
      ctaColor,
      ctaPaddingVertical,
      ctaPaddingHorizontal,
      contentAlignment,
      ctaText,
      ctaLink,
      onChangeCTA,
      ctaNoFollow,
      ctaNewTab,
      ctaHoverEffect,
      ctaHoverColor,
      ctaHoverTextColor,
    } = props;
    const styleBtn = getCTAButtonStyle(
      ctaBg,
      ctaColor,
      ctaPaddingVertical,
      ctaPaddingHorizontal
    );
    const toggleAlign =
      contentAlignment === 'left'
        ? 'flex-start'
        : contentAlignment === 'right'
          ? 'flex-end'
          : 'center';
    return createElement(
      'div',
      {},
      createElement(RichText, {
        tagName: 'a',
        value: ctaText,
        onChange: (val) => onChangeCTA({ ctaText: val }),
        placeholder: __('Escribe un CTA...', 'asap-theme'),
        style: styleBtn,
        className: 'cta-button',
        keepPlaceholderOnFocus: true,
      }),
      createElement(ManagedTextControl, {
        label: __('Enlace del Botón', 'asap-theme'),
        value: ctaLink,
        onChange: (val) => onChangeCTA({ ctaLink: val }),
        style: { maxWidth: '300px', marginBottom: '10px' },
      }),
      createElement(
        'div',
        { style: { display: 'flex', flexDirection: 'column', alignItems: toggleAlign } },
        createElement(ToggleControl, {
          label: __('Abrir en una pestaña nueva', 'asap-theme'),
          checked: ctaNewTab,
          onChange: (val) => onChangeCTA({ ctaNewTab: val }),
        }),
        createElement(ToggleControl, {
          label: __('Agregar atributo nofollow', 'asap-theme'),
          checked: ctaNoFollow,
          onChange: (val) => onChangeCTA({ ctaNoFollow: val }),
        })
      )
    );
  };

  // --------------------------------------------------------------------
  // REGISTRO DEL BLOQUE
  // --------------------------------------------------------------------

  registerBlockType('asap-theme/hero-block-full', {
    title: __('ASAP − Hero', 'asap-theme'),
    description: __('Una sección hero de ancho completo diseñada para cautivar a tu audiencia. Ofrece la posibilidad de personalizar el fondo con una imagen, color o video', 'asap-theme'),
    icon: 'cover-image',
    category: 'common',
    keywords: ['hero', 'asap', 'fullwidth'],
    supports: { align: ['full', 'wide'] },
    attributes: {
     heroTitle: {
       type: 'string',
       source: 'html',                // ← indica que contiene HTML
       selector: '.asap-herob-title'  // ← dónde extraerlo al volver a editar
     },
     heroSubtitle: {
       type: 'string',
       source: 'html',
       selector: '.asap-herob-subtitle'
     },
      ctaText: { type: 'string' },
      ctaLink: { type: 'string', default: '' },
      ctaBg: { type: 'string', default: '#0073aa' },
      ctaColor: { type: 'string', default: '#ffffff' },
      ctaNoFollow: { type: 'boolean', default: false },
      ctaNewTab: { type: 'boolean', default: false },
      ctaPaddingHorizontal: { type: 'number', default: 20 },
      ctaPaddingVertical: { type: 'number', default: 10 },
      ctaHoverEffect: { type: 'string', default: 'none' },
      ctaHoverColor: { type: 'string', default: '#005177' },
      ctaHoverTextColor: { type: 'string', default: '#ffffff' },
      backgroundType: { type: 'string', default: 'image' },
      backgroundColor: { type: 'string', default: '#222222' },
      backgroundImage: { type: 'string', default: '' },
      backgroundVideo: { type: 'string', default: '' },
      textColor: { type: 'string', default: '#ffffff' },
      backgroundImagePosition: { type: 'string', default: 'center' },
      headingFontSize: { type: 'number', default: 40 },
      paragraphFontSize: { type: 'number', default: 20 },
      mobileMaxHeight: { type: 'number', default: 400 },
      overlayEnabled: { type: 'boolean', default: true },
      overlayColor: { type: 'string', default: '#000000' },
      overlayOpacity: { type: 'number', default: 0.5 },
      heroHeight: { type: 'string', default: '500' },
      headingTag: { type: 'string', default: 'h1' },
      contentAlignment: { type: 'string', default: 'center' },
      innerWidth: { type: 'number', default: 800 },
      primaryColumnWidth: { type: 'number', default: 50 },
    },

    edit: (props) => {
      const { attributes, setAttributes } = props;
      const {
        heroTitle,
        heroSubtitle,
        ctaText,
        ctaLink,
        ctaBg,
        ctaColor,
        ctaNoFollow,
        ctaNewTab,
        ctaPaddingHorizontal,
        ctaPaddingVertical,
        ctaHoverEffect,
        ctaHoverColor,
        ctaHoverTextColor,
        backgroundType,
        backgroundColor,
        backgroundImage,
        backgroundVideo,
        textColor,
        backgroundImagePosition,
        headingFontSize,
        paragraphFontSize,
        mobileMaxHeight,
        overlayEnabled,
        overlayColor,
        overlayOpacity,
        heroHeight,
        headingTag,
        contentAlignment,
        innerWidth,
        primaryColumnWidth,
      } = attributes;

      const onChangeHeroTitle = (val) => setAttributes({ heroTitle: val });
      const onChangeHeroSubtitle = (val) => setAttributes({ heroSubtitle: val });
      const onChangeCTA = (data) => {
        setAttributes(data);
      };
      const onChangeCTABg = (val) => setAttributes({ ctaBg: val });
      const onChangeCTAColor = (val) => setAttributes({ ctaColor: val });
      const onChangeBackgroundType = (val) => setAttributes({ backgroundType: val });
      const onChangeBgColor = (val) => setAttributes({ backgroundColor: val });
      const onChangeBgImage = (media) => media.url && setAttributes({ backgroundImage: media.url });
      const onChangeBgVideo = (media) => media.url && setAttributes({ backgroundVideo: media.url });
      const onChangeTextColor = (val) => setAttributes({ textColor: val });
      const onChangeOverlayEnabled = (val) => setAttributes({ overlayEnabled: val });
      const onChangeOverlayColor = (val) => setAttributes({ overlayColor: val });
      const onChangeOverlayOpacity = (val) => setAttributes({ overlayOpacity: val });
      const onChangeHeroHeight = (val) => setAttributes({ heroHeight: String(val) });
      const onChangeHeadingTag = (val) => setAttributes({ headingTag: val });
      const onChangeCTAPaddingHorizontal = (val) => setAttributes({ ctaPaddingHorizontal: val });
      const onChangeCTAPaddingVertical = (val) => setAttributes({ ctaPaddingVertical: val });
      const onChangeHeadingFontSize = (val) => setAttributes({ headingFontSize: val });
      const onChangeParagraphFontSize = (val) => setAttributes({ paragraphFontSize: val });
      const onChangeMobileMaxHeight = (val) => setAttributes({ mobileMaxHeight: val });
      const onChangeBackgroundImagePosition = (val) => setAttributes({ backgroundImagePosition: val });
      const onChangeCTAHoverEffect = (val) => setAttributes({ ctaHoverEffect: val });
      const onChangeCTAHoverColor = (val) => setAttributes({ ctaHoverColor: val });
      const onChangeCTAHoverTextColor = (val) => setAttributes({ ctaHoverTextColor: val });
      const onChangeContentAlignment = (val) => setAttributes({ contentAlignment: val });
      const onChangeInnerWidth = (val) => setAttributes({ innerWidth: val });
      const onChangePrimaryColumnWidth = (val) => setAttributes({ primaryColumnWidth: val });

      const sidebar = document.querySelector('.edit-post-sidebar');
      const isSidebarOpen = sidebar && sidebar.classList.contains('is-open');

      const containerStyle = getContainerStyle(
        isSidebarOpen,
        textColor,
        heroHeight,
        backgroundType,
        backgroundColor,
        backgroundImage,
        backgroundImagePosition
      );
      const overlayDivStyle = getOverlayDivStyle(
        overlayEnabled,
        overlayColor,
        overlayOpacity
      );
      const contentStyle = {
        position: 'relative',
        zIndex: 2,
        width: '100%',
        maxWidth: innerWidth + 'px',
        margin: '0 auto',
        padding: '20px',
        textAlign: 'center',
      };

      // Inspector panels (idénticos a tu código original)
      const panelFondoOverlay = createElement(
        PanelBody,
        { title: __('Ajustes de fondo', 'asap-theme'), initialOpen: true },
        createElement(SelectControl, {
          label: __('Tipo de Fondo', 'asap-theme'),
          value: backgroundType,
          options: [
            { label: __('Color', 'asap-theme'), value: 'color' },
            { label: __('Imagen', 'asap-theme'), value: 'image' },
            { label: __('Video', 'asap-theme'), value: 'video' },
          ],
          onChange: onChangeBackgroundType,
          style: { marginBottom: '20px' },
        }),
        backgroundType === 'image' &&
          createElement(MediaUpload, {
            onSelect: onChangeBgImage,
            allowedTypes: ['image'],
            render: (obj) =>
              createElement(
                Button,
                {
                  isPrimary: true,
                  onClick: obj.open,
                  style: { marginBottom: '20px', marginLeft: '15px', marginTop: '-5px' },
                },
                backgroundImage ? __('Cambiar imagen', 'asap-theme') : __('Subir imagen', 'asap-theme')
              ),
          }),
        backgroundType === 'image' &&
          createElement(SelectControl, {
            label: __('Alineación de la imagen', 'asap-theme'),
            value: backgroundImagePosition,
            options: [
              { label: __('Arriba', 'asap-theme'), value: 'top' },
              { label: __('Centro', 'asap-theme'), value: 'center' },
              { label: __('Abajo', 'asap-theme'), value: 'bottom' },
            ],
            onChange: onChangeBackgroundImagePosition,
          }),
        backgroundType === 'video' &&
          createElement(MediaUpload, {
            onSelect: onChangeBgVideo,
            allowedTypes: ['video'],
            render: (obj) =>
              createElement(
                Button,
                {
                  isPrimary: true,
                  onClick: obj.open,
                  style: { marginBottom: '20px', marginLeft: '15px', marginTop: '-5px' },
                },
                backgroundVideo ? __('Cambiar video', 'asap-theme') : __('Subir video', 'asap-theme')
              ),
          }),
        createElement(ToggleControl, {
          label: __('Activar overlay', 'asap-theme'),
          checked: overlayEnabled,
          onChange: onChangeOverlayEnabled,
        }),
        overlayEnabled &&
          createElement(RangeControl, {
            label: __('Opacidad del Overlay', 'asap-theme'),
            value: overlayOpacity,
            onChange: onChangeOverlayOpacity,
            min: 0,
            max: 1,
            step: 0.05,
          })
      );

      const panelAlineacion = createElement(
        PanelBody,
        { title: __('Ajustes de alineación', 'asap-theme'), initialOpen: true },
        createElement(SelectControl, {
          label: __('Alinear contenido', 'asap-theme'),
          value: contentAlignment,
          options: [
            { label: __('Centro', 'asap-theme'), value: 'center' },
            { label: __('Izquierda', 'asap-theme'), value: 'left' },
            { label: __('Derecha', 'asap-theme'), value: 'right' },
          ],
          onChange: onChangeContentAlignment,
        }),
        createElement(RangeControl, {
          label: __('Ancho contenedor interno', 'asap-theme'),
          value: innerWidth,
          onChange: onChangeInnerWidth,
          min: 300,
          max: 1200,
          step: 10,
        }),
        contentAlignment !== 'center' &&
          createElement(RangeControl, {
            label: __('Porcentaje de columna principal (%)', 'asap-theme'),
            value: primaryColumnWidth,
            onChange: onChangePrimaryColumnWidth,
            min: 30,
            max: 70,
            step: 1,
          })
      );

      const panelTexto = createElement(
        PanelBody,
        { title: __('Ajustes de diseño', 'asap-theme'), initialOpen: false },
        createElement(SelectControl, {
          label: __('Etiqueta título', 'asap-theme'),
          value: headingTag,
          options: [
            { label: 'H1', value: 'h1' },
            { label: 'H2', value: 'h2' },
            { label: 'H3', value: 'h3' },
            { label: 'Párrafo', value: 'p' },
          ],
          onChange: onChangeHeadingTag,
        }),
        createElement(SelectControl, {
          label: __('Altura hero', 'asap-theme'),
          value: heroHeight === '100vh' ? 'full' : heroHeight === 'auto' ? 'auto' : 'custom',
          options: [
            { label: __('Pantalla Completa (100vh)', 'asap-theme'), value: 'full' },
            { label: __('Personalizado (px)', 'asap-theme'), value: 'custom' },
            { label: __('Auto (según contenido)', 'asap-theme'), value: 'auto' },
          ],
          onChange: (val) => {
            if (val === 'full') setAttributes({ heroHeight: '100vh' });
            else if (val === 'auto') setAttributes({ heroHeight: 'auto' });
            else setAttributes({ heroHeight: '500' });
          },
        }),
        heroHeight !== '100vh' && heroHeight !== 'auto' &&
          createElement(RangeControl, {
            label: __('Altura', 'asap-theme'),
            value: parseInt(heroHeight, 10),
            onChange: onChangeHeroHeight,
            min: 100,
            max: 1200,
          }),
        createElement(RangeControl, {
          label: __('Tamaño encabezado', 'asap-theme'),
          value: headingFontSize,
          onChange: onChangeHeadingFontSize,
          min: 20,
          max: 120,
          step: 1,
        }),
        createElement(RangeControl, {
          label: __('Tamaño párrafo', 'asap-theme'),
          value: paragraphFontSize,
          onChange: onChangeParagraphFontSize,
          min: 10,
          max: 80,
          step: 1,
        }),
        createElement(RangeControl, {
          label: __('Altura máxima en móviles', 'asap-theme'),
          value: mobileMaxHeight,
          onChange: onChangeMobileMaxHeight,
          min: 100,
          max: 800,
          step: 1,
        })
      );

      const panelCTA = createElement(
        PanelBody,
        { title: __('Botón (CTA)', 'asap-theme'), initialOpen: false },
        createElement(SelectControl, {
          label: __('Efecto Hover del Botón', 'asap-theme'),
          value: ctaHoverEffect,
          options: [
            { label: __('Ninguno', 'asap-theme'), value: 'none' },
            { label: __('Cambio de color', 'asap-theme'), value: 'color' },
            { label: __('Brillante', 'asap-theme'), value: 'gradient' },
          ],
          onChange: onChangeCTAHoverEffect,
        }),
        createElement(RangeControl, {
          label: __('Padding horizontal', 'asap-theme'),
          value: ctaPaddingHorizontal,
          onChange: onChangeCTAPaddingHorizontal,
          min: 0,
          max: 100,
        }),
        createElement(RangeControl, {
          label: __('Padding vertical', 'asap-theme'),
          value: ctaPaddingVertical,
          onChange: onChangeCTAPaddingVertical,
          min: 0,
          max: 100,
        })
      );

      const colorOptions = [
        { label: __('Fondo hero', 'asap-theme'), value: backgroundColor, onChange: onChangeBgColor },
        { label: __('Overlay', 'asap-theme'), value: overlayColor, onChange: onChangeOverlayColor },
        { label: __('Texto', 'asap-theme'), value: textColor, onChange: onChangeTextColor },
        { label: __('Fondo botón', 'asap-theme'), value: ctaBg, onChange: onChangeCTABg },
        { label: __('Color botón', 'asap-theme'), value: ctaColor, onChange: onChangeCTAColor },
      ];
      if (ctaHoverEffect === 'color') {
        colorOptions.push(
          { label: __('Fondo hover', 'asap-theme'), value: ctaHoverColor, onChange: onChangeCTAHoverColor },
          { label: __('Texto hover', 'asap-theme'), value: ctaHoverTextColor, onChange: onChangeCTAHoverTextColor }
        );
      }

      const panelColores = createElement(
        PanelBody,
        { title: __('Colores', 'asap-theme'), initialOpen: true },
        createElement(PanelColorSettings, {
          title: __('Opciones de colores', 'asap-theme'),
          initialOpen: true,
          colorSettings: colorOptions,
        })
      );

      const inspector = createElement(
        InspectorControls,
        {},
        panelFondoOverlay,
        panelColores,
        panelAlineacion,
        panelTexto,
        panelCTA
      );

      // Construcción del contenido interno
      let contentInner;
      if (contentAlignment === 'center') {
        contentInner = createElement(
          'div',
          { style: contentStyle },
          createElement(ManagedRichText, { tagName: headingTag, className: 'asap-herob-title', value: heroTitle, onChange: onChangeHeroTitle, placeholder: __('Escribe el título...', 'asap-theme'), style: { marginBottom: '20px', fontSize: headingFontSize + 'px', color: textColor } }),
          createElement(ManagedRichText, { tagName: 'p', className: 'asap-herob-subtitle', value: heroSubtitle, onChange: onChangeHeroSubtitle, placeholder: __('Escribe el subtítulo...', 'asap-theme'), style: { marginBottom: '20px', fontSize: paragraphFontSize + 'px', color: textColor } }),
          createElement(CTAButtonEdit, {
            ctaText,
            ctaLink,
            ctaBg,
            ctaColor,
            ctaNoFollow,
            ctaNewTab,
            ctaPaddingHorizontal,
            ctaPaddingVertical,
            onChangeCTA,
            ctaHoverEffect,
            ctaHoverColor,
            ctaHoverTextColor,
            contentAlignment,
          }),
          createElement(
            'div',
            { style: { marginTop: '20px', display: 'flex', justifyContent: 'center' } },
            createElement(InnerBlocks, { allowedBlocks: ['core/shortcode'], templateLock: false })
          )
        );
      } else {
        const primaryPercent = primaryColumnWidth;
        const secondaryPercent = 100 - primaryPercent;
        const primaryContent = createElement(
          'div',
          { style: { width: primaryPercent + '%', textAlign: contentAlignment, padding: '20px' } },
          createElement(ManagedRichText, { tagName: headingTag, className:'asap-herob-title', value: heroTitle, onChange: onChangeHeroTitle, placeholder: __('Escribe el título...', 'asap-theme'), style: { marginBottom: '20px', fontSize: headingFontSize + 'px', color: textColor } }),
          createElement(ManagedRichText, { tagName: 'p', className:'asap-herob-subtitle', value: heroSubtitle, onChange: onChangeHeroSubtitle, placeholder: __('Subtítulo...', 'asap-theme'), style: { marginBottom: '20px', fontSize: paragraphFontSize + 'px', color: textColor } }),
          createElement(CTAButtonEdit, {
            ctaText,
            ctaLink,
            ctaBg,
            ctaColor,
            ctaNoFollow,
            ctaNewTab,
            ctaPaddingHorizontal,
            ctaPaddingVertical,
            onChangeCTA,
            ctaHoverEffect,
            ctaHoverColor,
            ctaHoverTextColor,
            contentAlignment,
          })
        );
        const secondaryContent = createElement(
          'div',
          { style: { width: secondaryPercent + '%', textAlign: 'center', padding: '20px', display: 'flex', alignItems: 'center', justifyContent: 'center' } },
          createElement(InnerBlocks, { allowedBlocks: ['core/shortcode'], templateLock: false })
        );
        contentInner = createElement(
          'div',
          { style: { ...contentStyle, display: 'flex', alignItems: 'center' } },
          contentAlignment === 'left'
            ? [primaryContent, secondaryContent]
            : [secondaryContent, primaryContent]
        );
      }

      const containerEditor = createElement(
        'div',
        { className: 'asap-herob-editor', style: containerStyle },
        backgroundType === 'video' && backgroundVideo
          ? createElement(
              'div',
              { style: { position: 'absolute', top: 0, left: 0, right: 0, bottom: 0, backgroundColor: '#000', opacity: 0.3 } },
              __('(Vista previa de video no real)', 'asap-theme')
            )
          : null,
        overlayEnabled && createElement('div', { style: overlayDivStyle }),
        contentInner
      );

      return createElement(Fragment, {}, inspector, containerEditor);
    },

    save: (props) => {
      const { attributes } = props;
      const {
        heroTitle = '',
        heroSubtitle = '',
        ctaText = '',
        ctaLink = '#',
        ctaBg = '#0073aa',
        ctaColor = '#ffffff',
        ctaNoFollow,
        ctaNewTab,
        ctaPaddingHorizontal,
        ctaPaddingVertical,
        ctaHoverEffect,
        ctaHoverColor,
        ctaHoverTextColor,
        backgroundType = 'image',
        backgroundColor = '#222222',
        backgroundImage = '',
        backgroundVideo = '',
        textColor = '#ffffff',
        headingFontSize,
        paragraphFontSize,
        mobileMaxHeight,
        overlayEnabled,
        overlayColor,
        overlayOpacity,
        heroHeight = '500',
        headingTag = 'h1',
        backgroundImagePosition = 'center',
        contentAlignment,
        innerWidth,
        primaryColumnWidth,
      } = attributes;

      const containerStyle = {
        width: '100vw',
        marginLeft: 'calc(-50vw + 50%)',
        position: 'relative',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        textAlign: 'center',
        color: textColor,
        overflow: 'hidden',
        marginBottom: '26px',
      };
      if (heroHeight === '100vh') {
        containerStyle.height = '100vh';
      } else if (heroHeight === 'auto') {
        containerStyle.height = 'auto';
      } else {
        containerStyle.height = heroHeight + 'px';
      }
      if (backgroundType === 'color') {
        containerStyle.backgroundColor = backgroundColor;
      }

      let backgroundHTML = '';
      if (backgroundType === 'video' && backgroundVideo) {
        backgroundHTML += `<video class="asap-herob-video" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;" src="${backgroundVideo}" autoplay muted loop playsinline></video>`;
      } else if (backgroundType === 'image' && backgroundImage) {
        backgroundHTML += `<picture class="asap-herob-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;"><img class="asap-herob-img" src="${backgroundImage}" alt="" style="width:100%;height:100%;object-fit:cover;object-position:${backgroundImagePosition};"/></picture>`;
      }
      const backgroundEl = backgroundHTML ? createElement('div', { dangerouslySetInnerHTML: { __html: backgroundHTML } }) : null;

      // Aquí definimos overlayEl para **todos** los casos
      const overlayEl = overlayEnabled
        ? createElement('div', {
            className: 'asap-herob-overlay',
            style: {
              position: 'absolute',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              background: rgbaColor(overlayColor, overlayOpacity),
            },
          })
        : null;

   // ——— Ajustes responsive de altura **y** tipografías ———
   const headingMobile = Math.max(10, (headingFontSize || 40) - 10);   // ‑10 px
   const paragraphMobile = Math.max(8, (paragraphFontSize || 20) - 2); //  ‑2 px

   const mobileStyle = createElement('style', {
     dangerouslySetInnerHTML: {
       __html: `
         @media only screen and (max-width: 767px){
           .asap-herob-block-full.alignfull{ max-height:${mobileMaxHeight}px; }
           .asap-herob-block-full h1 { font-size:${headingMobile}px !important; }
           .asap-herob-block-full h1 + p{ font-size:${paragraphMobile}px !important; }
         }`,
     },
   });
      const ctaHoverStyleEl = getCTAHoverStyle(ctaHoverEffect, ctaHoverColor, ctaHoverTextColor)
        ? createElement('style', { dangerouslySetInnerHTML: { __html: getCTAHoverStyle(ctaHoverEffect, ctaHoverColor, ctaHoverTextColor) } })
        : null;

      if (contentAlignment === 'center') {
        const textContent = createElement(
          'div',
          {
            className: 'asap-herob-content',
            style: {
              position: 'relative',
              zIndex: 2,
              width: '100%',
              maxWidth: innerWidth + 'px',
              margin: '0 auto',
              padding: '20px',
              textAlign: 'center',
            },
          },
          heroTitle &&
            createElement(headingTag, {
              className: 'asap-herob-title',
              style: { marginBottom: '20px', fontSize: headingFontSize + 'px', color: textColor },
              dangerouslySetInnerHTML: { __html: heroTitle },
            }),
          heroSubtitle &&
            createElement('p', {
              className: 'asap-herob-subtitle', 
              style: { marginBottom: '20px', fontSize: paragraphFontSize + 'px', color: textColor },
              dangerouslySetInnerHTML: { __html: heroSubtitle },
            }),
          ctaText &&
            createElement(
              'a',
              {
                href: ctaLink,
                className: 'cta-button',
                style: {
                  display: 'inline-block',
                  padding: `${ctaPaddingVertical}px ${ctaPaddingHorizontal}px`,
                  borderRadius: '4px',
                  textDecoration: 'none',
                  background: ctaBg,
                  color: ctaColor,
                },
              },
              ctaText
            )
        );
        const shortcodeContent = createElement(
          'div',
          { className: 'asap-herob-shortcode', style: { marginTop: '20px', textAlign: 'center' } },
          createElement(InnerBlocks.Content)
        );
        const contentContainer = createElement('div', {}, textContent, shortcodeContent);

        return createElement(
          Fragment,
          {},
          createElement(
            'div',
            { className: 'asap-herob-block-full alignfull', style: containerStyle },
            backgroundEl,
            overlayEl,
            contentContainer
          ),
          mobileStyle,
          ctaHoverStyleEl
        );
      } else {
        const primaryPercent = primaryColumnWidth;
        const secondaryPercent = 100 - primaryPercent;
        const primaryColumnEl = createElement(
          'div',
          {
            className: 'asap-herob-primary',
            style: { width: primaryPercent + '%', textAlign: contentAlignment, padding: '20px' },
          },
          heroTitle &&
            createElement(headingTag, {
              className: 'asap-herob-title',
              style: { marginBottom: '20px', fontSize: headingFontSize + 'px', color: textColor },
              dangerouslySetInnerHTML: { __html: heroTitle },
            }),
          heroSubtitle &&
            createElement('p', {
               className: 'asap-herob-subtitle',
              style: { marginBottom: '20px', fontSize: paragraphFontSize + 'px', color: textColor },
              dangerouslySetInnerHTML: { __html: heroSubtitle },
            }),
          ctaText &&
            createElement(
              'a',
              {
                href: ctaLink,
                className: 'cta-button',
                style: {
                  display: 'inline-block',
                  padding: `${ctaPaddingVertical}px ${ctaPaddingHorizontal}px`,
                  borderRadius: '4px',
                  textDecoration: 'none',
                  background: ctaBg,
                  color: ctaColor,
                },
              },
              ctaText
            )
        );
        const secondaryColumnEl = createElement(
          'div',
          {
            className: 'asap-herob-secondary',
            style: {
              width: secondaryPercent + '%',
              textAlign: 'center',
              padding: '20px',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            },
          },
          createElement(InnerBlocks.Content)
        );
        const contentContainer = createElement(
          'div',
          {
            className: 'asap-herob-content',
            style: {
              position: 'relative',
              zIndex: 2,
              display: 'flex',
              alignItems: 'center',
              width: '100%',
              maxWidth: innerWidth + 'px',
              margin: '0 auto',
              padding: '20px',
            },
          },
          contentAlignment === 'left'
            ? [primaryColumnEl, secondaryColumnEl]
            : [secondaryColumnEl, primaryColumnEl]
        );

        return createElement(
          Fragment,
          {},
          createElement(
            'div',
            { className: 'asap-herob-block-full alignfull', style: containerStyle },
            backgroundEl,
            overlayEl,
            contentContainer
          ),
          mobileStyle,
          ctaHoverStyleEl
        );
      }
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.editor, window.wp.components, window.wp.i18n, window.wp.blockEditor);
