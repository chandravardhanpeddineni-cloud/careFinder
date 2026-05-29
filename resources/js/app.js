import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const providers = [
    {
        name: 'Dr. Meera Shah',
        specialty: 'Primary Care',
        location: 'Downtown Health Clinic',
        city: 'Ahmedabad',
        rating: '4.9',
        availability: 'Today',
        time: '3:30 PM',
        distance: '1.2 km',
        image: 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
    },
    {
        name: 'Dr. Arjun Patel',
        specialty: 'Cardiology',
        location: 'HeartFirst Medical Center',
        city: 'Surat',
        rating: '4.8',
        availability: 'Tomorrow',
        time: '10:00 AM',
        distance: '3.8 km',
        image: 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&w=400&q=80',
    },
    {
        name: 'Dr. Nisha Rao',
        specialty: 'Pediatrics',
        location: 'Little Steps Clinic',
        city: 'Vadodara',
        rating: '4.9',
        availability: 'Today',
        time: '5:15 PM',
        distance: '2.4 km',
        image: 'https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&fit=crop&w=400&q=80',
    },
    {
        name: 'Dr. Kabir Menon',
        specialty: 'Dermatology',
        location: 'ClearSkin Care Studio',
        city: 'Rajkot',
        rating: '4.7',
        availability: 'This week',
        time: 'Friday, 11:45 AM',
        distance: '5.1 km',
        image: 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&w=400&q=80',
    },
];

const providerList = document.querySelector('#providerList');
const resultCount = document.querySelector('#resultCount');
const searchInput = document.querySelector('#searchInput');
const specialtyFilter = document.querySelector('#specialtyFilter');
const availabilityFilter = document.querySelector('#availabilityFilter');
const appointmentSummary = document.querySelector('#appointmentSummary');

if (providerList && resultCount && searchInput && specialtyFilter && availabilityFilter && appointmentSummary) {
    const providerMatches = (provider) => {
        const searchTerm = searchInput.value.trim().toLowerCase();
        const specialty = specialtyFilter.value;
        const availability = availabilityFilter.value;
        const searchable = [
            provider.name,
            provider.specialty,
            provider.location,
            provider.city,
        ].join(' ').toLowerCase();

        return (
            (!searchTerm || searchable.includes(searchTerm)) &&
            (specialty === 'all' || provider.specialty === specialty) &&
            (availability === 'all' || provider.availability === availability)
        );
    };

    const selectProvider = (provider) => {
        appointmentSummary.className = 'appointment-card';
        appointmentSummary.innerHTML = `
            <strong>${provider.name}</strong>
            <dl>
                <div>
                    <dt>Specialty</dt>
                    <dd>${provider.specialty}</dd>
                </div>
                <div>
                    <dt>Clinic</dt>
                    <dd>${provider.location}</dd>
                </div>
                <div>
                    <dt>Time</dt>
                    <dd>${provider.availability}, ${provider.time}</dd>
                </div>
                <div>
                    <dt>Distance</dt>
                    <dd>${provider.distance}</dd>
                </div>
            </dl>
        `;
    };

    const renderProviders = () => {
        const visibleProviders = providers.filter(providerMatches);
        resultCount.textContent = `${visibleProviders.length} ${visibleProviders.length === 1 ? 'result' : 'results'}`;

        if (!visibleProviders.length) {
            providerList.innerHTML = '<div class="no-results">No matching providers. Try a different specialty or availability.</div>';
            return;
        }

        providerList.innerHTML = visibleProviders
            .map((provider, index) => `
                <article class="provider-card">
                    <div class="provider-photo" style="background-image: url('${provider.image}')" role="img" aria-label="${provider.name}"></div>
                    <div class="provider-meta">
                        <h3>${provider.name}</h3>
                        <p>${provider.specialty} at ${provider.location}, ${provider.city}</p>
                        <div class="detail-row">
                            <span class="pill">Rating ${provider.rating}</span>
                            <span class="pill blue">${provider.availability}</span>
                            <span class="pill amber">${provider.distance}</span>
                        </div>
                    </div>
                    <button class="book-button" type="button" data-provider-index="${index}">Book</button>
                </article>
            `)
            .join('');

        document.querySelectorAll('[data-provider-index]').forEach((button) => {
            button.addEventListener('click', () => {
                const selectedProvider = visibleProviders[Number(button.dataset.providerIndex)];
                selectProvider(selectedProvider);
            });
        });
    };

    [searchInput, specialtyFilter, availabilityFilter].forEach((control) => {
        control.addEventListener('input', renderProviders);
    });

    renderProviders();
}
