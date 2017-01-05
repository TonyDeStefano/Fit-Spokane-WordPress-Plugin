<?php

namespace FitSpokane;

class Coupon {

	const POST_TYPE = 'fit_spokane_coupon';
	const META_CODE = 'coupon_code';
	const META_AMOUNT_OFF = 'coupon_amount_off';
	const META_PERCENT_OFF = 'coupon_percent_off';
	const META_STARTS_AT = 'coupon_starts_at';
	const META_ENDS_AT = 'coupon_ends_at';
	const META_PROGRAM_IDS = 'coupon_program_ids';

	private $id;
	private $title;
	private $code;
	private $amount_off;
	private $percent_off;
	private $starts_at;
	private $ends_at;
	private $program_ids = array();

	/**
	 * Coupon constructor.
	 *
	 * @param null $id
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
			else
			{
				$this->setId( NULL );
			}
		}
	}

	/**
	 * @param \WP_Post $post
	 */
	public function loadFromPost( \WP_Post $post )
	{
		$meta = get_post_meta( $post->ID );

		$this
			->setId( $post->ID )
			->setTitle( $post->post_title )
			->setCode( isset( $meta[ self::META_CODE ][ 0 ] ) ? $meta[ self::META_CODE ][ 0 ] : NULL )
			->setAmountOff( isset( $meta[ self::META_AMOUNT_OFF ][ 0 ] ) ? $meta[ self::META_AMOUNT_OFF ][ 0 ] : NULL )
			->setPercentOff( isset( $meta[ self::META_PERCENT_OFF ][ 0 ] ) ? $meta[ self::META_PERCENT_OFF ][ 0 ] : NULL )
			->setStartsAt( isset( $meta[ self::META_STARTS_AT ][ 0 ] ) ? $meta[ self::META_STARTS_AT ][ 0 ] : NULL )
			->setEndsAt( isset( $meta[ self::META_ENDS_AT ][ 0 ] ) ? $meta[ self::META_ENDS_AT ][ 0 ] : NULL )
			->setProgramIds( isset( $meta[ self::META_PROGRAM_IDS ][ 0 ] ) ? $meta[ self::META_PROGRAM_IDS ][ 0 ] : NULL );
	}

	/**
	 *
	 */
	public function create()
	{
		$this->update();
	}

	/**
	 *
	 */
	public function update()
	{
		if ( $this->id !== NULL )
		{
			update_post_meta( $this->id, self::META_CODE, $this->getCode() );
			update_post_meta( $this->id, self::META_AMOUNT_OFF, $this->getAmountOff() );
			update_post_meta( $this->id, self::META_PERCENT_OFF, $this->getPercentOff() );
			update_post_meta( $this->id, self::META_STARTS_AT, $this->getStartsAt() );
			update_post_meta( $this->id, self::META_ENDS_AT, $this->getEndsAt() );
			update_post_meta( $this->id, self::META_PROGRAM_IDS, $this->getProgramIds( TRUE ) );
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
	 * @return Coupon
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
	 * @return Coupon
	 */
	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return ( $this->code === NULL ) ? '' : $this->code;
	}

	/**
	 * @param mixed $code
	 *
	 * @return Coupon
	 */
	public function setCode( $code )
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAmountOff()
	{
		return ( $this->amount_off === NULL ) ? 0 : $this->amount_off;
	}

	/**
	 * @param mixed $amount_off
	 *
	 * @return Coupon
	 */
	public function setAmountOff( $amount_off )
	{
		$amount_off = preg_replace( '/[^0-9.]*/ ', '', $amount_off );
		$this->amount_off = ( is_numeric( $amount_off ) ) ? floatval( $amount_off ) : NULL;

		return $this;
	}

	/**
	 * @param bool $as_decimal
	 *
	 * @return float|int
	 */
	public function getPercentOff( $as_decimal = FALSE )
	{
		$divide_by = ( $as_decimal ) ? 100 : 1;

		return ( $this->percent_off === NULL ) ? 0 : $this->percent_off / $divide_by;
	}

	/**
	 * @param mixed $percent_off
	 *
	 * @return Coupon
	 */
	public function setPercentOff( $percent_off )
	{
		$percent_off = preg_replace( '/[^0-9.]*/ ', '', $percent_off );
		$this->percent_off = ( is_numeric( $percent_off ) ) ? floatval( $percent_off ) : NULL;

		return $this;
	}

	/**
	 * @param string $format
	 *
	 * @return mixed
	 */
	public function getStartsAt( $format = 'Y-m-d' )
	{
		return ( $this->starts_at === NULL ) ? NULL : date( $format, strtotime( $this->starts_at ) );
	}

	/**
	 * @param mixed $starts_at
	 *
	 * @return Coupon
	 */
	public function setStartsAt( $starts_at )
	{
		if ( strlen( $starts_at ) == 0 )
		{
			$this->starts_at = NULL;
		}
		else
		{
			$this->starts_at = ( is_numeric( $starts_at ) ) ? date( 'Y-m-d', $starts_at ) : date( 'Y-m-d', strtotime( $starts_at ) );
		}

		return $this;
	}

	/**
	 * @param string $format
	 *
	 * @return mixed
	 */
	public function getEndsAt( $format = 'Y-m-d' )
	{
		return ( $this->ends_at === NULL ) ? NULL : date( $format, strtotime( $this->ends_at ) );
	}

	/**
	 * @param mixed $ends_at
	 *
	 * @return Coupon
	 */
	public function setEndsAt( $ends_at )
	{
		if ( strlen( $ends_at ) == 0 )
		{
			$this->ends_at = NULL;
		}
		else
		{
			$this->ends_at = ( is_numeric( $ends_at ) ) ? date( 'Y-m-d', $ends_at ) : date( 'Y-m-d', strtotime( $ends_at ) );
		}

		return $this;
	}

	/**
	 * @param bool $as_json
	 *
	 * @return array|string
	 */
	public function getProgramIds( $as_json = FALSE )
	{
		return ( $as_json ) ? json_encode( $this->program_ids ) : $this->program_ids;
	}

	/**
	 * @param array|string $program_ids
	 *
	 * @return Coupon
	 */
	public function setProgramIds( $program_ids )
	{
		$this->program_ids = ( is_array( $program_ids ) ) ? $program_ids : json_decode( $program_ids, TRUE );

		if ( ! is_array( $this->program_ids ) )
		{
			$this->program_ids = array();
		}

		return $this;
	}

	/**
	 * @param $program_id
	 *
	 * @return Coupon
	 */
	public function addProgramId( $program_id )
	{
		if ( is_numeric( $program_id ) )
		{
			$this->program_ids[] = intval( $program_id );
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValidForAllPrograms()
	{
		foreach ( $this->program_ids as $program_id )
		{
			if ( $program_id == 0 )
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @return Coupon[]
	 */
	public static function getAllCoupons()
	{
		/** @var \WP_Post $post */
		global $post;

		$coupons = array();

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
			$coupon = new Coupon;
			$coupon->loadFromPost( $post );
			$coupons[ $coupon->getId() ] = $coupon;
		}

		return $coupons;
	}

	/**
	 * @param $code
	 *
	 * @return bool|Coupon
	 */
	public static function getCouponByCode( $code )
	{
		$coupons = self::getAllCoupons();

		foreach ( $coupons as $coupon )
		{
			if ( $coupon->getCode() == $code )
			{
				return $coupon;
			}
		}

		return FALSE;
	}

	/**
	 * @return string
	 */
	public function getDiscountText()
	{
		if ( $this->getAmountOff() > 0 )
		{
			return '$' . number_format( $this->getPercentOff(), 2 ) . ' off';
		}
		else
		{
			return number_format( $this->getPercentOff() ) . '% off';
		}
	}

	/**
	 * @return string
	 */
	public function getValidDates()
	{
		if ( $this->starts_at === NULL && $this->ends_at === NULL )
		{
			return 'Valid Now, No Expiration';
		}
		else
		{
			return 'Valid ' . ( ( $this->starts_at !== NULL ) ? $this->getStartsAt( 'n/j/Y' ) : '' ) . ( ( $this->ends_at !== NULL ) ? ' until ' . $this->getEndsAt( 'n/j/Y' ) : '' );
		}
	}

	public function isValidNow()
	{
		if ( $this->starts_at === NULL && $this->ends_at === NULL )
		{
			return TRUE;
		}

		if ( $this->starts_at === NULL && time() <= strtotime( $this->ends_at ) )
		{
			return TRUE;
		}

		if ( $this->ends_at === NULL && time() >= strtotime( $this->starts_at ) )
		{
			return TRUE;
		}

		if ( time() <= strtotime( $this->ends_at ) && time() >= strtotime( $this->starts_at ) )
		{
			return TRUE;
		}

		return FALSE;
	}
}