<?php

namespace FM\RFMBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2018 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Configuration implements ConfigurationInterface
{
    private $defaultExtensionRestrictions = [
        "",
        "jpg",
        "jpe",
        "jpeg",
        "gif",
        "png",
        "svg",
        "txt",
        "pdf",
        "odp",
        "ods",
        "odt",
        "rtf",
        "doc",
        "docx",
        "xls",
        "xlsx",
        "ppt",
        "pptx",
        "csv",
        "ogv",
        "avi",
        "mkv",
        "mp4",
        "webm",
        "m4v",
        "ogg",
        "mp3",
        "wav",
        "zip",
        "md",
    ];

    private $defaultPatternRestrictions = [
            // files
            "*/.htaccess",
            "*/web.config",
            // folders
            "*/.CDN_ACCESS_LOGS/*",
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('fm_rfm');

        $rootNode
            ->fixXmlConfig('instance')
            ->children()
                ->scalarNode('configuration_provider')->defaultValue('fm_rfm.configurator.default')->end()
                ->scalarNode('assets_path')->defaultValue('assets/richfilemanager')->end()
                ->scalarNode('loader')->defaultValue('fm_rfm.loader.default')->end()
                ->arrayNode('instances')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path_prefix')->defaultValue('/')->end()
                            ->scalarNode('theme')->defaultValue('flat-dark')->end()
                            ->integerNode('mkdir_mode')->defaultValue(755)->end()
                            ->append($this->createClientConfigNode())
                            ->scalarNode('editor')->defaultValue('simple')->end()
                            ->arrayNode('logger')
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('enabled')->defaultTrue()->end()
                                        ->scalarNode('logger_file')->defaultNull()->end()
                                    ->end()
                            ->end()
                            ->arrayNode('options')
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->booleanNode('serverRoot')->defaultFalse()->end()
                                        ->scalarNode('fileRoot')->defaultValue('false')->end()
                                        ->integerNode('fileRootSizeLimit')->defaultValue(0)->end()
                                        ->booleanNode('charsLatinOnly')->defaultFalse()->end()
                                    ->end()
                            ->end()
                            ->arrayNode('security')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('readOnly')->defaultFalse()->end()
                                    ->booleanNode('normalizeFilename')->defaultTrue()->end()
                                    ->arrayNode('extensions')
                                        ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('policy')
                                                    ->isRequired()
                                                    ->defaultValue('ALLOW_LIST')
                                                ->end() // driver
                                                ->arrayNode('restrictions')
                                                    ->beforeNormalization()
                                                    ->ifTrue(function ($v) {
                                                        return is_string($v);
                                                    })
                                                    ->then(function ($v) {
                                                        return array_map('trim', explode(',', $v));
                                                    })
                                                    ->end()
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue($this->defaultExtensionRestrictions)
                                                ->end()
                                            ->end()
                                    ->end()
                                    ->arrayNode('patterns')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('policy')->defaultValue('DISALLOW_LIST')->end()
                                            ->booleanNode('ignoreCase')->defaultTrue()->end()
                                            ->arrayNode('restrictions')
                                                ->beforeNormalization()
                                                ->ifTrue(function ($v) {
                                                    return is_string($v);
                                                })
                                                ->then(function ($v) {
                                                    return array_map('trim', explode(',', $v));
                                                })
                                                ->end()
                                                ->prototype('scalar')->end()
                                            ->defaultValue($this->defaultPatternRestrictions)
                                            ->end()
                                    ->arrayNode('symlinks')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->booleanNode('allowAll')->defaultFalse()->end()
                                            ->scalarNode('allowPaths')->defaultValue('')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('upload')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->integerNode('fileSizeLimit')->defaultValue(16000000)->end()
                                        ->booleanNode('overwrite')->defaultFalse()->end()
                                        ->scalarNode('paramName')->defaultValue('upload')->end()
                                ->arrayNode('images')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('main')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->booleanNode('autoOrient')->defaultTrue()->end()
                                                ->integerNode('maxWidth')->defaultValue(1280)->end()
                                                ->integerNode('maxWidth')->defaultValue(1024)->end()
                                        ->arrayNode('thumbnail')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->booleanNode('enabled')->defaultTrue()->end()
                                                ->booleanNode('cache')->defaultTrue()->end()
                                                ->scalarNode('dir')->defaultValue("_thumbs/")->end()
                                                ->booleanNode('crop')->defaultTrue()->end()
                                                ->integerNode('maxWidth')->defaultValue(64)->end()
                                                ->integerNode('maxWidth')->defaultValue(64)->end()
                                        ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->end()
                ->end();

        return $treeBuilder;
    }

    protected $defaultAvailableLanguages =
        ["ar", "bs", "ca", "cs", "da", "de", "el", "en", "es", "fa", "fi", "fr", "he", "hu", "it", "ja", "nl", "pl", "pt", "ru", "sv", "th", "tr", "vi", "zh-CN", "zh-TW"];

    protected $defaultCapabilities = [
        "select",
        "upload",
        "download",
        "rename",
        "copy",
        "move",
        "delete",
        "extract",
        "createFolder"];

    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition the clientConfig node
     */
    private function createClientConfigNode()
    {
        return $this->createNode('client_config')
                ->addDefaultsIfNotSet()
                ->children()
                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('theme')->defaultValue('flat-dark')->end()
                        ->booleanNode('showTitleAttr')->defaultFalse()->end()
                        ->booleanNode('showConfirmation')->defaultTrue()->end()
                        ->booleanNode('browseOnly')->defaultFalse()->end()
                        ->scalarNode('fileSorting')->defaultValue('NAME_ASC')->end()
                        ->scalarNode('folderPosition')->defaultValue('bottom')->end()
                        ->booleanNode('quickSelect')->defaultFalse()->end()
                        ->booleanNode('logger')->defaultFalse()->end()
                        ->booleanNode('allowFolderDownload')->defaultTrue()->end()
                        ->booleanNode('allowChangeExtensions')->defaultFalse()->end()
                        ->arrayNode('capabilities')
                            ->beforeNormalization()
                                ->ifTrue(function ($v) {
                                    return is_string($v);
                                })
                                ->then(function ($v) {
                                    return array_map('trim', explode(',', $v));
                                })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue($this->defaultCapabilities)->end()
                        ->end()
                ->end()
                ->arrayNode('language')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')->defaultValue('en')->end()
                        ->arrayNode('available')
                            ->beforeNormalization()
                                ->ifTrue(function ($v) {
                                    return is_string($v);
                                })
                                ->then(function ($v) {
                                    return array_map('trim', explode(',', $v));
                                })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue($this->defaultAvailableLanguages)->end()
                        ->end()
                ->end()
                ->arrayNode('formatter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('datetime_skeleton')->defaultValue('yMMMdHm')->end()
                    ->end()
                ->end()
                ->arrayNode('filetree')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('foldersOnly')->defaultFalse()->end()
                        ->booleanNode('reloadOnClick')->defaultTrue()->end()
                        ->integerNode('expandSpeed')->defaultValue(200)->end()
                        ->booleanNode('showLine')->defaultTrue()->end()
                        ->integerNode('width')->defaultValue(200)->end()
                        ->integerNode('minWidth')->defaultValue(200)->end()
                    ->end()
                ->end()
                ->arrayNode('manager')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('defaultViewMode')->defaultValue('grid')->end()
                        ->booleanNode('dblClickOpen')->defaultFalse()->end()
                        ->arrayNode('selection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('useCtrlKey')->defaultTrue()->end()
                            ->end()
                        ->end()
                        ->arrayNode('renderer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('position')->defaultFalse()->end()
                                ->scalarNode('indexFile')->defaultValue('readme.md')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('multiple')->defaultTrue()->end()
                        ->integerNode('maxNumberOfFiles')->defaultValue(5)->end()
                        ->integerNode('chunkSize')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->arrayNode('clipboard')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('encodeCopyUrl')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('filter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('image')
                            ->beforeNormalization()
                                ->ifTrue(function ($v) {
                                    return is_string($v);
                                })
                                ->then(function ($v) {
                                    return array_map('trim', explode(',', $v));
                                })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue(["jpg", "jpeg", "gif", "png", "svg"])->end()
                        ->arrayNode('media')
                            ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_string($v);
                            })
                            ->then(function ($v) {
                                return array_map('trim', explode(',', $v));
                            })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue(["ogv", "avi", "mkv", "mp4", "webm", "m4v", "ogg", "mp3", "wav"])->end()
                        ->arrayNode('office')
                            ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_string($v);
                            })
                            ->then(function ($v) {
                                return array_map('trim', explode(',', $v));
                            })
                            ->end()
                            ->prototype('scalar')->end()
                        ->defaultValue(["txt", "pdf", "odp", "ods", "odt", "rtf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "csv", "md"])->end()
                        ->arrayNode('archive')
                            ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_string($v);
                            })
                            ->then(function ($v) {
                                return array_map('trim', explode(',', $v));
                            })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue(["zip", "tar", "rar"])->end()
                        ->arrayNode('audio')
                            ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_string($v);
                            })
                            ->then(function ($v) {
                                return array_map('trim', explode(',', $v));
                            })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue(["ogg", "mp3", "wav"])->end()
                        ->arrayNode('video')
                            ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_string($v);
                            })
                            ->then(function ($v) {
                                return array_map('trim', explode(',', $v));
                            })
                            ->end()
                            ->prototype('scalar')->end()
                            ->defaultValue(["ogv", "avi", "mkv", "mp4", "webm", "m4v"])->end()
                    ->end()
                ->end()
                ->arrayNode('search')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('recursive')->defaultFalse()->end()
                        ->booleanNode('caseSensitive')->defaultFalse()->end()
                        ->integerNode('typingDelay')->defaultValue(500)->end()
                    ->end()
                ->end()
                ->arrayNode('viewer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('absolutePath')->defaultTrue()->end()
                        ->booleanNode('previewUrl')->defaultFalse()->end()
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('lazyLoad')->defaultTrue()->end()
                                ->booleanNode('showThumbs')->defaultTrue()->end()
                                ->integerNode('thumbMaxWidth')->defaultValue(64)->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["jpg", "jpe", "jpeg", "gif", "png", "svg"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('video')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->integerNode('playerWidth')->defaultValue(400)->end()
                                ->integerNode('playerHeight')->defaultValue(222)->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["ogv", "mp4", "webm", "m4v"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('audio')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["ogg", "mp3", "wav"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('iframe')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('readerWidth')->defaultValue("95%")->end()
                                ->scalarNode('readerHeight')->defaultValue("600")->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["htm", "html"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('opendoc')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('readerWidth')->defaultValue("640")->end()
                                ->scalarNode('readerHeight')->defaultValue("480")->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["pdf", "odt", "odp", "ods"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('google')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('readerWidth')->defaultValue("640")->end()
                                ->scalarNode('readerHeight')->defaultValue("480")->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["doc", "docx", "xls", "xlsx", "ppt", "pptx"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('codeMirrorRenderer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["txt", "csv"])->end()
                            ->end()
                        ->end()
                        ->arrayNode('markdownRenderer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->arrayNode('extensions')
                                    ->beforeNormalization()
                                    ->ifTrue(function ($v) {
                                        return is_string($v);
                                    })
                                    ->then(function ($v) {
                                        return array_map('trim', explode(',', $v));
                                    })
                                    ->end()
                                    ->prototype('scalar')->end()
                                    ->defaultValue(["md"])->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('editor')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
                    ->scalarNode('theme')->defaultValue('default')->end()
                    ->booleanNode('lineNumbers')->defaultTrue()->end()
                    ->booleanNode('lineWrapping')->defaultTrue()->end()
                    ->booleanNode('codeHighlight')->defaultTrue()->end()
                    ->booleanNode('matchBrackets')->defaultTrue()->end()
                    ->arrayNode('extensions')
                        ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return is_string($v);
                        })
                        ->then(function ($v) {
                            return array_map('trim', explode(',', $v));
                        })
                        ->end()
                        ->prototype('scalar')->end()
                    ->defaultValue(["html", "txt", "csv", "md"])->end()
                ->end()
            ->end()
            ->arrayNode('customScrollbar')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
                    ->scalarNode('theme')->defaultValue('inset-2-dark')->end()
                    ->booleanNode('button')->defaultTrue()->end()
                ->end()
            ->end()
            ->arrayNode('extras')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('extra_js')
                        ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return is_string($v);
                        })
                        ->then(function ($v) {
                            return array_map('trim', explode(',', $v));
                        })
                        ->end()
                        ->prototype('scalar')->end()
                    ->defaultValue([])->end()
                    ->booleanNode('extra_js_async')->defaultTrue()->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Creates a node.
     *
     * @param string $name the node name
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition the node
     */
    private function createNode($name)
    {
        return $this->createTreeBuilder()->root($name);
    }

    /**
     * Creates a tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder the tree builder
     */
    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }
}
