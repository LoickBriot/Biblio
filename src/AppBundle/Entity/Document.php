<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isPrivate;

    public function __construct()
    {
        $this->isPrivate = true;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du r�pertoire o� les documents upload�s doivent �tre sauvegard�s
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se d�barrasse de � __DIR__ � afin de ne pas avoir de probl�me lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour g�n�rer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
            $this->name = $this->file->getClientOriginalName();
        }
        var_dump('pdftotext -f 1 -l 1 '  . $this->getAbsolutePath() .' '. $this->getUploadRootDir() .'/pdf_' . str_replace(".pdf","",$this->path) . '/0.txt');

    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du d�placement du fichier, une exception
        // va automatiquement �tre lanc�e par la m�thode move(). Cela va emp�cher
        // proprement l'entit� d'�tre persist�e dans la base de donn�es si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);
        if (!is_dir($this->getUploadRootDir() .'/pdf_' . str_replace(".pdf","",$this->path))){
            mkdir($this->getUploadRootDir() .'/pdf_' . str_replace(".pdf","",$this->path));
        }

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function pageNumber(){
        $pdf = $this->getAbsolutePath();
        $pages = null;
        if ( false !== ( $file = file_get_contents( $pdf ) ) ) {
            $pages = preg_match_all( "/\/Page\W/", $file, $matches );
        }
        return $pages;
    }


}