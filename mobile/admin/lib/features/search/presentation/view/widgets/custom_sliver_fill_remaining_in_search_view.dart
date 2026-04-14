import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/search/presentation/view/widgets/custom_generate_previously_search_in_search_view.dart';

class CustomSliverFillRemainingInSearchView extends StatelessWidget {
  const CustomSliverFillRemainingInSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            // Heights.height20(context: context),
            // const CustomHeaderSectionInSearchView(),
            // Heights.height41(context: context),
            // const CustomTextWithAlignAndPaddingInSearchView(
            //   text: 'البحث مؤخرا',
            // ),
            Heights.height25(context: context),
            const CustomGeneratePreviouslySearchInSearchView(),
          ],
        ),
      ),
    );
  }
}
