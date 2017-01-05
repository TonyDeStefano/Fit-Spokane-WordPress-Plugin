<?php

namespace FitSpokane;

class Program {

	const POST_TYPE = 'fit_spokane_program';

	const PROP_IS_VISIBLE = 'is_visible';
	const PROP_PRICE = 'price';
	const PROP_IS_RECURRING = 'is_recurring';
	const PROP_RECUR_PERIOD = 'recur_period';

	private $id;
	private $title;
	private $price;
	private $is_visible = FALSE;
	private $is_recurring = FALSE;
	private $recur_period;

	/**
	 * Program constructor.
	 *
	 * @param $id
	 */
	public function __construct( $id = NULL )
	{
		$this
			->setId( $id )
			->read();
	}

	/**
	 *
	 */
	public function read()
	{
		if ( $this->id !== NULL )
		{
			if ( $post = get_post( $this->id ) )
			{
				$this->loadFromPost( $post );
			}

		}
	}

	/**
	 * @param \WP_Post $post
	 */
	public function loadFromPost( \WP_Post $post )
	{
		$this
			->setId( $post->ID )
			->setTitle( $post->post_title );

		$custom = get_post_custom( $this->id );

		if ( ! empty( $custom ) )
		{
			if ( isset( $custom[ self::PROP_IS_VISIBLE ] ) )
			{
				$this->setIsVisible( $custom[ self::PROP_IS_VISIBLE ][ 0 ] );
			}

			if ( isset( $custom[ self::PROP_PRICE ] ) )
			{
				$this->setPrice( $custom[ self::PROP_PRICE ][ 0 ] );
			}

			if ( isset( $custom[ self::PROP_IS_RECURRING ] ) )
			{
				$this->setIsRecurring( $custom[ self::PROP_IS_RECURRING ][ 0 ] );
			}

			if ( isset( $custom[ self::PROP_RECUR_PERIOD ] ) )
			{
				$this->setRecurPeriod( $custom[ self::PROP_RECUR_PERIOD ][ 0 ] );
			}
		}
	}

	public function update()
	{
		if ( $this->id !== NULL )
		{
			update_post_meta( $this->id, self::PROP_IS_VISIBLE, ( $this->isVisible() ) ? 1 : 0 );
			update_post_meta( $this->id, self::PROP_IS_RECURRING, ( $this->isRecurring() ) ? 1 : 0 );
			update_post_meta( $this->id, self::PROP_PRICE, $this->getPrice() );
			update_post_meta( $this->id, self::PROP_RECUR_PERIOD, $this->getRecurPeriod() );
		}
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return Program
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return ( $this->title === NULL ) ? '' : $this->title;
	}

	/**
	 * @param mixed $title
	 *
	 * @return Program
	 */
	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return ( $this->price === NULL ) ? 0 : $this->price;
	}

	/**
	 * @param mixed $price
	 *
	 * @return Program
	 */
	public function setPrice( $price )
	{
		$price = preg_replace( '/[^0-9\.]/', '', (string) $price );

		$this->price = ( is_numeric ( $price ) ) ? round( $price, 2 ) : 0;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isVisible()
	{
		return ( $this->is_visible === TRUE );
	}

	/**
	 * @param boolean $is_visible
	 *
	 * @return Program
	 */
	public function setIsVisible( $is_visible )
	{
		$this->is_visible = ( $is_visible === TRUE || $is_visible == 1 );

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isRecurring()
	{
		return ( $this->is_recurring === TRUE );
	}

	/**
	 * @param boolean $is_recurring
	 *
	 * @return Program
	 */
	public function setIsRecurring( $is_recurring )
	{
		$this->is_recurring = ( $is_recurring === TRUE || $is_recurring == 1 );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRecurPeriod()
	{
		return ( $this->recur_period === NULL || ! $this->isRecurring() ) ? 1 : $this->recur_period;
	}

	/**
	 * @param mixed $recur_period
	 *
	 * @return Program
	 */
	public function setRecurPeriod( $recur_period )
	{
		$this->recur_period = is_numeric( $recur_period ) ? intval( $recur_period ) : 1;

		return $this;
	}

	/**
	 * @return Program[]
	 */
	public static function getAllPrograms()
	{
		/** @var \WP_Post $post */
		global $post;

		$programs = array();

		$query = new \WP_Query( array(
			'post_type' => self::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));


		while ( $query->have_posts() )
		{
			$query->the_post();
			$program = new Program;
			$program->loadFromPost( $post );
			$programs[ $program->getId() ] = $program;
		}

		return $programs;
	}
}
