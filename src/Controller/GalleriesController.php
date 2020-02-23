<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ConflictException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotAcceptableException;
use Cake\Http\Exception\NotFoundException;

/**
 * Galleries Controller
 *
 * @property \App\Model\Table\GalleriesTable $Galleries
 *
 * @method \App\Model\Entity\Gallery[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GalleriesController extends AppController
{
    /**
     * {@inheritDoc}
     * @throws \Cake\Http\Exception\NotAcceptableException when client does not accept or request JSON response
     */
    public function beforeFilter(Event $event)
    {
        $acceptsJson = $this->request->accepts('application/json');
        if (!$this->request->is('json') && !$acceptsJson) {
            throw new NotAcceptableException(__("Client must accept or request JSON response"));
        }

        return parent::beforeFilter($event);
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $galleries = $this->Galleries->find('all', [
            'fields' => ['name'],
        ]);

        $this->set(compact('galleries'));
        $this->set('_serialize', ['galleries']);

        return null;
    }

    /**
     * View method
     *
     * @return \Cake\Http\Response|null
     */
    public function view()
    {
        $gallery = $this->Galleries->findByName(rawurldecode($this->request->getParam('path')))
            ->contain(['Images'])
            ->first();

        if (empty($gallery)) {
            throw new NotFoundException(__("Gallery not found"));
        }

        $this->set('gallery', [
            'gallery' => [
                'path' => $gallery->path,
                'name' => $gallery->name,
            ],
            'images' => $this->Galleries->prepareImagesData($gallery),
        ]);
        $this->set('_serialize', 'gallery');

        return null;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $gallery = $this->Galleries->newEntity();
        if ($this->request->is('post')) {
            $gallery = $this->Galleries->patchEntity($gallery, $this->request->getData());

            $errors = $gallery->errors();

            if (empty($errors)) {
                if ($this->Galleries->findByName($gallery->name)->count()) {
                    throw new ConflictException(__("Gallery with this name already exists"));
                }

                if ($this->Galleries->save($gallery)) {
                    $this->response->statusCode(201);

                    $this->set('path', $gallery->get('path'));
                    $this->set('name', $gallery->name);
                    $this->set('_serialize', ['name', 'path']);

                    return null;
                }
            } else {
                $this->response->statusCode(400);

                $description = [];

                foreach ($errors as $field => $field_errors) {
                    foreach ($field_errors as $error) {
                        $description[] = $field . ': ' . $error;
                    }
                }

                $description = "Bad JSON object: " . join(', ', $description);

                $this->set('data', [
                    'code' => 400,
                    'name' => 'INVALID_SCHEMA',
                    'description' => $description,
                ]);
                $this->set('_serialize', 'data');

                return null;
            }
        }

        throw new InternalErrorException(__("Undefined error"));
    }

    /**
     * Upload method
     *
     * @return \Cake\Http\Response|null
     */
    public function upload()
    {
        $this->request->allowMethod(['post']);

        $gallery = $this->Galleries->findByName(rawurldecode($this->request->getParam('path')))->first();

        if (empty($gallery)) {
            throw new NotFoundException(__("Gallery not found"));
        }

        if (count($this->request->getData()) == 0) {
            throw new BadRequestException(__("Image for upload not found"));
        }

        $savedImages = [];

        foreach ($this->request->getData() as $name => $file) {
            $image = $this->Galleries->Images->newEntity([
                'gallery_id' => $gallery->id,
                'name' => $name,
                'filename' => $file,
            ]);

            if (!$this->Galleries->Images->save($image)) {
                throw new InternalErrorException(__("Undefined error"));
            }

            $savedImages[] = $image;
        }

        $this->response->statusCode(201);

        $gallery->images = $savedImages;

        $this->set('data', [
            'uploaded' => $this->Galleries->prepareImagesData($gallery),
        ]);
        $this->set('_serialize', 'data');

        return null;
    }

    /**
     * Delete method
     *
     * @return \Cake\Http\Response|null
     */
    public function delete()
    {
        $this->request->allowMethod(['delete']);

        $gallery = $this->Galleries->findByName(rawurldecode($this->request->getParam('path')))->first();

        if (empty($gallery)) {
            throw new NotFoundException(__("Gallery not found"));
        }

        if ($this->Galleries->delete($gallery)) {
            $this->response->withStatus(200, __("Gallery successfully deleted"));
            $this->set('_serialize', []);

            return null;
        }

        throw new InternalErrorException(__("Undefined error"));
    }
}
