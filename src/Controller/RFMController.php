<?php

namespace FM\RFMBundle\Controller;

use FM\RFMBundle\Loader\RFMLoader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RFMController.
 */
class RFMController extends Controller
{

    /**
     * Loader service init.
     *
     * @param string $instance
     * @return void
     */
    public function loadAction($instance)
    {
        $loader = $this->get('fm_rfm.loader');
        $loader->setInstance($instance);
        $loader->load();
    }

    public function showAction(Request $request, $instance)
    {
        $parameters = $this->container->getParameter('fm_rfm');
        $assetsPath = $parameters['assets_path'];
        $parameters = $parameters['instances'][$instance];

        if (empty($parameters['locale'])) {
            $parameters['locale'] = $request->getLocale();
        }

        $result = $this->selectEditor($parameters, $instance, $assetsPath, $request->get('id'));

        return $this->render($result['template'], $result['params']);
    }

    public function configAction($instance = 'default')
    {
        $config = $this->get('fm_rfm.configurator')->getClientConfiguration($instance);

        return $this->json($config);
    }

    public function configDefaultAction()
    {
        return $this->json([]);
    }

    /**
     * @param array  $parameters
     * @param string $instance
     * @param $assetsPath
     * @param null $formTypeId
     *
     * @return array
     */
    private function selectEditor($parameters, $instance, $assetsPath, $formTypeId = null)
    {
        $editor         = $parameters['editor'];
        $locale         = $parameters['locale'] ?: $this->container->getParameter('locale');
        $pathPrefix     = $parameters['path_prefix'];
        $theme          = $parameters['theme'];
        $result         = array();

        switch ($editor) {
            case 'ckeditor':
                $result['template'] = '@FMElfinder/Elfinder/ckeditor.html.twig';
                $result['params']   = array(
                    'locale'        => $locale,
                    'instance'      => $instance,
                    'prefix'        => $assetsPath,
                    'theme'         => $theme,
                    'pathPrefix'    => $pathPrefix,
                );

                return $result;
            case 'form':
                $result['template'] = '@FMRFM/RFM/elfinder_type.html.twig';
                $result['params']   = array(
                    'locale'        => $locale,
                    'instance'      => $instance,
                    'id'            => $formTypeId,
                    'prefix'        => $assetsPath,
                    'theme'         => $theme,
                    'pathPrefix'    => $pathPrefix,
                );

                return $result;
            default:
                $result['template'] = '@FMRFM/RFM/simple.html.twig';
                $result['params']   = array(
                    'locale'        => $locale,
                    'instance'      => $instance,
                    'prefix'        => $assetsPath,
                    'theme'         => $theme,
                    'pathPrefix'    => $pathPrefix,
                );

                return $result;
        }
    }

}