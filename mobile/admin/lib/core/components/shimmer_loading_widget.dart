import 'package:flutter/material.dart';
import 'package:shimmer_animation/shimmer_animation.dart';

/// Wraps [child] in a single coordinated shimmer sweep (Facebook/YouTube style).
/// All [ShimmerBox] and [ShimmerCircle] children animate together.
class ShimmerLoadingWidget extends StatelessWidget {
  const ShimmerLoadingWidget({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return Shimmer(
      duration: const Duration(milliseconds: 1400),
      color: Colors.white,
      colorOpacity: 0.55,
      enabled: true,
      direction: const ShimmerDirection.fromLTRB(),
      child: child,
    );
  }
}

/// A rectangular grey placeholder — use inside [ShimmerLoadingWidget].
class ShimmerBox extends StatelessWidget {
  const ShimmerBox({
    super.key,
    this.width = double.infinity,
    required this.height,
    this.borderRadius = 8.0,
  });
  final double width;
  final double height;
  final double borderRadius;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        color: const Color(0xffE0E0E0),
        borderRadius: BorderRadius.circular(borderRadius),
      ),
    );
  }
}

/// A circular grey placeholder — use inside [ShimmerLoadingWidget].
class ShimmerCircle extends StatelessWidget {
  const ShimmerCircle({super.key, required this.diameter});
  final double diameter;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: diameter,
      height: diameter,
      decoration: const BoxDecoration(
        color: Color(0xffE0E0E0),
        shape: BoxShape.circle,
      ),
    );
  }
}
