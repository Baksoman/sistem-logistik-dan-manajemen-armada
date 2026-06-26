from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
import searoute as sr
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(
    title="Searoute Microservice",
    description="Kalkulasi rute laut menggunakan library searoute",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


class RouteRequest(BaseModel):
    origin_lon: float
    origin_lat: float
    destination_lon: float
    destination_lat: float
    units: Optional[str] = "km"


class RouteResponse(BaseModel):
    distance: float
    units: str
    geojson: dict


@app.get("/health")
def health_check():
    return {"status": "ok", "service": "searoute-microservice"}


@app.post("/route/sea", response_model=RouteResponse)
def calculate_sea_route(request: RouteRequest):
    """
    Kalkulasi rute laut antara dua koordinat.
    Gunakan format [longitude, latitude].
    """
    try:
        origin = [request.origin_lon, request.origin_lat]
        destination = [request.destination_lon, request.destination_lat]

        # Calculate the route using the searoute library
        route = sr.searoute(origin, destination, units=request.units)

        if not route or 'properties' not in route:
            raise ValueError("Titik koordinat terlalu jauh dari laut atau rute laut tidak ditemukan.")

        return RouteResponse(
            distance=route["properties"]["length"],
            units=route["properties"]["units"],
            geojson=route
        )
    except Exception as e:
        error_msg = str(e)
        logger.error(f"Error calculating sea route: {error_msg}")
        
        if "cannot add waypoints" in error_msg.lower() or "too far from sea" in error_msg.lower():
            friendly_msg = "Gagal menghitung rute laut. Pastikan titik Origin dan Destination berada di area perairan/pelabuhan, bukan di daratan jauh."
            raise HTTPException(status_code=400, detail=friendly_msg)
            
        raise HTTPException(status_code=500, detail=f"Failed to calculate sea route: {error_msg}")
