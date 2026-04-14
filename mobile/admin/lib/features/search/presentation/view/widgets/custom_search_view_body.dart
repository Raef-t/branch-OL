import 'package:flutter/material.dart';
import '/features/search/presentation/view/widgets/custom_sliver_app_bar_in_search_view.dart';
import '/features/search/presentation/view/widgets/custom_sliver_fill_remaining_in_search_view.dart';

class CustomSearchViewBody extends StatelessWidget {
  const CustomSearchViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return const CustomScrollView(
      slivers: [
        CustomSliverAppBarInSearchView(),
        CustomSliverFillRemainingInSearchView(),
      ],
    );
  }
}
