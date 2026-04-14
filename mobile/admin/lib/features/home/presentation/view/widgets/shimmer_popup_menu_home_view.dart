import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';

/// Skeleton loader for the branch-selector popup menu button.
/// Matches the shape of [CustomIconAndTextInPopupMenuItemHomeView]:
///   Row → small arrow icon + branch name text.
class ShimmerPopupMenuHomeView extends StatelessWidget {
  const ShimmerPopupMenuHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    return ShimmerLoadingWidget(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 10.0),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const ShimmerBox(width: 16, height: 16, borderRadius: 4),
            SizedBox(width: size.width * 0.02),
            ShimmerBox(width: size.width * 0.22, height: 14, borderRadius: 7),
          ],
        ),
      ),
    );
  }
}
