<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotFoundException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 *
 * @method \App\Model\Entity\Image[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImagesController extends AppController
{
    /**
     * View method
     *
     * @return \Cake\Http\Response|null
     */
    public function view()
    {
        if (!preg_match("/^(\d+)x(\d+)$/i", $this->request->getParam('params'), $matches)) {
            throw new BadRequestException(__("Invalid width or height"));
        }

        $width = (int)$matches[1];
        $height = (int)$matches[2];

        $gallery = $this->Images->Galleries->findByName($this->request->getParam('gallery'))->first();
        if (empty($gallery)) {
            throw new NotFoundException(__("Gallery not found"));
        }

        $image = $this->Images->find('all', [
            'conditions' => [
                'filename' => $this->request->getParam('path'),
                'gallery_id' => $gallery->id,
            ],
        ])->first();
        if (empty($image)) {
            throw new NotFoundException(__("Image not found"));
        }

        $imagePath = WWW_ROOT . "files/Images/filename" . DS . $image->get('filename');

        try {
            $imagine = new Imagine();
            $image = $imagine->open($imagePath);

            //resize
            if ($width != 0 && $height != 0) {
                $image->resize(new Box($width, $height));
            } elseif ($width != 0) {
                $image->resize($image->getSize()->widen($width));
            } elseif ($height != 0) {
                $image->resize($image->getSize()->heighten($height));
            }

            $options = [
                'jpeg_quality' => 90,
            ];

            return $this->response->withType('image/jpeg')->withStringBody($image->get('jpg', $options));
        } catch (\Exception $e) {
            throw new InternalErrorException(__("Imagine error"));
        }
    }
}
