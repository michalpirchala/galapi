<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\BadRequestException;

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

        $width = $matches[1];
        $height = $matches[2];

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

        $this->set('image', $image);

        return null;
    }
}
