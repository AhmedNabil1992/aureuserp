<?php

namespace Webkul\Software\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Software\Http\Requests\TicketReplyRequest;
use Webkul\Software\Http\Requests\TicketRequest;
use Webkul\Software\Http\Resources\V1\TicketEventResource;
use Webkul\Software\Http\Resources\V1\TicketResource;
use Webkul\Software\Models\Ticket;
use Webkul\Software\Services\TicketService;

#[Group('Support Tickets')]
#[Authenticated]
class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService) {}

    /**
     * List all tickets.
     */
    public function index(Request $request): ResourceCollection
    {
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('assigned_to'),
            ])
            ->allowedSorts(['ticket_number', 'created_at', 'updated_at', 'status', 'priority'])
            ->allowedIncludes(['partner', 'program', 'license', 'assignedTo', 'attachments'])
            ->defaultSort('-updated_at')
            ->paginate($request->integer('per_page', 20));

        return TicketResource::collection($tickets);
    }

    /**
     * Create a new ticket.
     */
    public function store(TicketRequest $request): TicketResource
    {
        $data = $request->validated();
        $paths = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $this->ticketService->storeUploadedFile($file);
            }
        }

        $data['creator_id'] = Auth::id();
        unset($data['attachments']);

        $ticket = $this->ticketService->createTicket($data, $paths);

        return new TicketResource($ticket->load(['partner', 'program', 'license', 'attachments']));
    }

    /**
     * View a single ticket.
     */
    public function show(Ticket $ticket): TicketResource
    {
        return new TicketResource(
            $ticket->load(['partner', 'program', 'license', 'assignedTo', 'attachments', 'events.attachments', 'events.user', 'events.partner'])
        );
    }

    /**
     * Update a ticket (status, priority, assigned_to).
     */
    public function update(TicketRequest $request, Ticket $ticket): TicketResource
    {
        $ticket->update($request->validated());

        return new TicketResource($ticket->refresh()->load(['partner', 'program', 'license', 'attachments']));
    }

    /**
     * Delete a ticket.
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully.']);
    }

    /**
     * Add a reply to a ticket.
     */
    #[Subgroup('Ticket Replies')]
    public function reply(TicketReplyRequest $request, Ticket $ticket): TicketEventResource
    {
        $data = $request->validated();
        $paths = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $this->ticketService->storeUploadedFile($file);
            }
        }

        $eventData = [
            'content'    => $data['content'],
            'type'       => 'message',
            'is_private' => $data['is_private'] ?? false,
            'user_id'    => Auth::id(),
        ];

        unset($data['attachments']);

        $event = $this->ticketService->replyToTicket($ticket, $eventData, $paths);

        return new TicketEventResource($event->load(['user', 'attachments']));
    }

    /**
     * List replies for a ticket.
     */
    #[Subgroup('Ticket Replies')]
    public function replies(Ticket $ticket): ResourceCollection
    {
        $events = $ticket->events()
            ->with(['user', 'partner', 'attachments'])
            ->latest()
            ->get();

        return TicketEventResource::collection($events);
    }
}
