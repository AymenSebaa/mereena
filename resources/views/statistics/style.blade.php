@push('scripts')
    <style>
        .extra-arrival {
            background: rgba(59, 130, 246, .15);
            color: #3b82f6;
        }

        .extra-departure {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        .extra-boarding {
            background: rgba(245, 158, 11, .15);
            color: #f59e0b;
        }

        .extra-none {
            background: rgba(107, 114, 128, .15);
            color: #6b7280;
        }

        .badge-extra {
            display: inline-block;
            padding: 0.25em 0.5em;
            border-radius: 1em;
            font-size: .8em;
            text-transform: capitalize;
        }

        .badge-extra.arrival {
            background-color: #3b82f6;
            color: white;
        }

        .badge-extra.departure {
            background-color: #10b981;
            color: white;
        }

        .badge-extra.boarding {
            background-color: #f59e0b;
            color: white;
        }

        .badge-extra.none {
            background-color: #6b7280;
            color: white;
        }

        .group-text {
            background: #fff3 !important;
        }

        .duplicate-text {
            padding-left: 2em !important;
            opacity: .5;
        }
    </style>
@endpush
