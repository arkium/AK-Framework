<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

abstract class Entity implements \ArrayAccess {

	protected $erreurs = array(), $id;

	public function __construct(array $donnees = array()) {
		if (!empty($donnees)) {
			$this->hydrate($donnees);
		}
	}

	public function isNew() {
		return empty($this->id);
	}

	public function erreurs() {
		return $this->erreurs;
	}

	public function id() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	public function hydrate(array $donnees) {
		foreach ($donnees as $attribut => $valeur) {
			$methode = 'set' . ucfirst($attribut);

			if (is_callable(array(
					$this,
					$methode
			))) {
				$this->$methode($valeur);
			}
		}
	}

	/**
     * Interface permettant d'accéder aux objets de la même façon que pour les tableaux
     * @param mixed $var
     * @return mixed
     */
    public function offsetGet($var): mixed
    {
        if (
            isset($this->$var) && is_callable(
                array(
                    $this,
                    $var
                )
            )
        ) {
            return $this->$var();
        } else
            return null;
    }

    public function offsetSet($var, $value): void
    {
        $method = 'set' . ucfirst($var);

        if (
            isset($this->$var) && is_callable(
                array(
                    $this,
                    $method
                )
            )
        ) {
            $this->$method($value);
        }
    }

    public function offsetExists($var): bool
    {
        return isset($this->$var) && is_callable(
            array(
                $this,
                $var
            )
        );
    }

    public function offsetUnset($var): void
    {
        throw new \Exception('Impossible de supprimer une quelconque valeur');
    }
}