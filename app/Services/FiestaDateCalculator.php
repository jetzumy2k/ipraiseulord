<?php

namespace App\Services;

use App\Models\Fiesta;
use Carbon\Carbon;
use InvalidArgumentException;

class FiestaDateCalculator
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function eventsForMonth(int $year, int $month, ?string $category = null): array
    {
        $events = [];

        foreach ($this->fixedEvents($month, $category) as $event) {
            $events[] = $this->formatEvent($event, Carbon::create($year, $month, $event['day']));
        }

        foreach ($this->movableEvents($category) as $event) {
            $date = $this->resolveMovableDate($event['movable_rule'], $year);

            if ((int) $date->month === $month) {
                $events[] = $this->formatEvent($event, $date);
            }
        }

        usort($events, fn (array $a, array $b) => $a['day'] <=> $b['day'] ?: $a['sort_order'] <=> $b['sort_order']);

        return $events;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function eventsForYear(int $year, ?string $category = null): array
    {
        $events = [];

        for ($month = 1; $month <= 12; $month++) {
            foreach ($this->eventsForMonth($year, $month, $category) as $event) {
                $events[] = $event;
            }
        }

        return $events;
    }

    public function resolveMovableDate(string $rule, int $year): Carbon
    {
        $easter = $this->easterSunday($year);

        return match ($rule) {
            'ash_wednesday' => $easter->copy()->subDays(46),
            'palm_sunday' => $easter->copy()->subDays(7),
            'holy_thursday' => $easter->copy()->subDays(3),
            'good_friday' => $easter->copy()->subDays(2),
            'holy_saturday' => $easter->copy()->subDay(),
            'easter_sunday' => $easter->copy(),
            'divine_mercy_sunday' => $easter->copy()->addWeek(),
            'ascension' => $easter->copy()->addDays(39),
            'pentecost' => $easter->copy()->addDays(49),
            'trinity_sunday' => $easter->copy()->addDays(56),
            'corpus_christi' => $easter->copy()->addDays(60),
            'sacred_heart' => $easter->copy()->addDays(68),
            'christ_the_king' => $this->christTheKingSunday($year),
            default => throw new InvalidArgumentException("Unknown movable feast rule [{$rule}]."),
        };
    }

    public function easterSunday(int $year): Carbon
    {
        $timestamp = easter_date($year);

        return Carbon::createFromTimestamp($timestamp)->startOfDay();
    }

    protected function christTheKingSunday(int $year): Carbon
    {
        $date = Carbon::create($year, 11, 1)->endOfMonth()->startOfDay();

        while ((int) $date->dayOfWeek !== Carbon::SUNDAY) {
            $date->subDay();
        }

        return $date;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fixedEvents(int $month, ?string $category): array
    {
        $query = Fiesta::query()
            ->where('is_active', true)
            ->where('is_movable', false)
            ->where('month', $month)
            ->orderBy('day')
            ->orderBy('sort_order');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->map(fn (Fiesta $fiesta) => $this->eventPayload($fiesta))->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function movableEvents(?string $category): array
    {
        $query = Fiesta::query()
            ->where('is_active', true)
            ->where('is_movable', true)
            ->orderBy('sort_order');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->map(fn (Fiesta $fiesta) => $this->eventPayload($fiesta))->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function eventPayload(Fiesta $fiesta): array
    {
        return [
            'id' => $fiesta->id,
            'title' => $fiesta->title,
            'category' => $fiesta->category,
            'honoree_name' => $fiesta->honoree_name,
            'description' => $fiesta->description,
            'liturgical_rank' => $fiesta->liturgical_rank,
            'is_movable' => $fiesta->is_movable,
            'movable_rule' => $fiesta->movable_rule,
            'sort_order' => $fiesta->sort_order,
            'month' => $fiesta->month,
            'day' => $fiesta->day,
        ];
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>
     */
    protected function formatEvent(array $event, Carbon $date): array
    {
        return [
            ...$event,
            'date' => $date->toDateString(),
            'month' => (int) $date->month,
            'day' => (int) $date->day,
            'weekday' => $date->format('l'),
        ];
    }
}
