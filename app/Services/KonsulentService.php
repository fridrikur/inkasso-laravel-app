<?php

namespace App\Services;

use App\Models\Konsulenter;
use App\Models\HovedKonsulent;
use App\Models\SkjultKonsulent;
use App\Models\NotifikationsKonsulent;

class KonsulentService
{
    public function __construct(
        protected ActivityService $activity,
        protected TransferService $transfer,
    ) {}
    /*
    |--------------------------------------------------------------------------
    | Create / Update
    |--------------------------------------------------------------------------
    */


    public function save(
        ?Konsulenter $konsulent,
        array $data
    ): Konsulenter {


        /*
        |--------------------------------------------------------------------------
        | Update existing
        |--------------------------------------------------------------------------
        */

        if ($konsulent) {


            $old = $konsulent->only([
                'navn',
                'email',
                'tlf',
                'mobil',
            ]);


            $konsulent->update($data);


            $changes = [];


            foreach ($data as $field => $value) {

                if (($old[$field] ?? null) != $value) {

                    $changes[$field] = [
                        'old' => $old[$field] ?? null,
                        'new' => $value,
                    ];

                }

            }


            if ($changes) {

                $this->activity->log(
                    'Konsulent opdateret',
                    $konsulent,
                    $changes
                );

            }


            return $konsulent->refresh();

        }



        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */


        $konsulent = Konsulenter::create($data);


        $this->activity->log(
            'Ny konsulent oprettet',
            $konsulent,
            [
                'navn' => $konsulent->navn,
                'email' => $konsulent->email,
            ]
        );


        return $konsulent;

    }





    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */


    public function syncRoles(
        Konsulenter $k,
        array $roles
    ): void {


        $oldRoles = [

            'hoved' =>
                HovedKonsulent::current()?->id === $k->id,

            'skjult' =>
                SkjultKonsulent::has($k),

            'notifikation' =>
                NotifikationsKonsulent::has($k),

        ];



        /*
        |--------------------------------------------------------------------------
        | Hoved
        |--------------------------------------------------------------------------
        */


        if ($roles['hoved'] ?? false) {


            HovedKonsulent::setHoved($k);

            NotifikationsKonsulent::add($k);

            SkjultKonsulent::remove($k);


        } else {


            if(
                HovedKonsulent::current()?->id === $k->id
            ){

                HovedKonsulent::unsetHoved();

            }

        }



        /*
        |--------------------------------------------------------------------------
        | Skjult
        |--------------------------------------------------------------------------
        */


        if ($roles['skjult'] ?? false) {


            if(
                HovedKonsulent::current()?->id !== $k->id
            ){

                SkjultKonsulent::add($k);

            }


        } else {


            SkjultKonsulent::remove($k);

        }




        /*
        |--------------------------------------------------------------------------
        | Notifikation
        |--------------------------------------------------------------------------
        */


        if ($roles['notifikation'] ?? false) {


            NotifikationsKonsulent::add($k);


        } else {


            if(
                HovedKonsulent::current()?->id !== $k->id
            ){

                NotifikationsKonsulent::remove($k);

            }

        }





        $newRoles = [

            'hoved' =>
                HovedKonsulent::current()?->id === $k->id,

            'skjult' =>
                SkjultKonsulent::has($k),

            'notifikation' =>
                NotifikationsKonsulent::has($k),

        ];



        if($oldRoles !== $newRoles) {


            $this->activity->log(
                'Konsulentroller ændret',
                $k,
                [
                    'fra' => $oldRoles,
                    'til' => $newRoles,
                ]
            );

        }

        if ($roles['hoved'] ?? false) {

            HovedKonsulent::setHoved($k);

            NotifikationsKonsulent::add($k);

            SkjultKonsulent::remove($k);

        }

    }





    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
        public function transferAndDelete(
            Konsulenter $from,
            Konsulenter $to
        ): void
        {
            DB::transaction(function () use ($from, $to) {

                $count = $this->transfer
                    ->transferKonsulent($from, $to);

                $this->activity->log(
                    'Konsulent overført',
                    $from,
                    [
                        'til' => $to->navn,
                        'antal_sager' => $count,
                    ]
                );

                $this->delete($from);

            });
        }

    public function delete(
        Konsulenter $k
    ): void {


        $this->activity->log(
            'Konsulent slettet',
            $k,
            [
                'navn' => $k->navn,
                'email' => $k->email,
            ]
        );



        SkjultKonsulent::remove($k);

        NotifikationsKonsulent::remove($k);



        if(
            HovedKonsulent::current()?->id === $k->id
        ){

            HovedKonsulent::unsetHoved();

        }



        $k->delete();

    }

}